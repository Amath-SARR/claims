<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Mail\NotifyUserToNewClaimAttributed;
use App\Mail\SendMailToClaimantClaimsResolved;
use App\Models\Application;
use App\Models\CategorieReclamation;
use App\Models\Comment;
use App\Models\Priorite;
use App\Models\Reclamation;
use App\Models\ReclamationState;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PharIo\Manifest\Author;

class ReclamationController extends Controller
{
    public function __construct()
    {
        $this->middleware('gestionnaire')->except(['destroy', 'store', 'getSatisfactionReclamation', 'getInsatisfactionReclamation']);
        $this->middleware('admin')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reclamations = Reclamation::all();
        return CustomResponse::buildSuccessResponse($reclamations);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $states = State::where('code', '<>', 'CLASSED')->orderBy('ordre', 'asc')->get();
        $tab_reclamations = [];
        foreach ($states as $state) {
            $query = Reclamation::whereStateId($state->id);
            if (!Auth::user()->is_admin) {
                $query->whereHas('categorieReclamation.gestionnaires', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            }
            if ($request->exists('responsableIds') && count($request->responsableIds) > 0) {
                $responsableIds = $request->responsableIds;
                $query->where(function ($query) use ($responsableIds) {
                    $query->whereIn('user_id', $responsableIds);
                    if (in_array(0, $responsableIds)) {
                        $query->orWhereNull('user_id');
                    }
                });
            }
            if ($request->exists('applicationId') && $request->applicationId != 0) {
                $applicationId = $request->applicationId;
                $query->whereRelation('categorieReclamation', 'application_id', $applicationId);
            }
            $reclamations = $query->get();
            $truncatedReclamations = [];
            foreach ($reclamations as $reclamation) {
                $tmp = (object) array(
                    'id' => $reclamation->id,
                    'uid'=> $reclamation->uid,
                    'number' => $reclamation->number,
                    'objet' => $reclamation->objet,
                    'user_name' => $reclamation->user->name ?? null,
                    'user_photo_full_path' => $reclamation->user->photo_full_path ?? null,
                    'priorite_nom' => $reclamation->priorite->nom,
                    'priorite_class' => $reclamation->priorite->class,
                    'priorite_icone' => $reclamation->priorite->icone,
                    'application_logo_full_path' => $reclamation->categorieReclamation->application->logo_full_path,
                    'application_code' => $reclamation->categorieReclamation->application->code,
                    'application_nom' => $reclamation->categorieReclamation->application->nom,
                );
                $truncatedReclamations[] = $tmp;
            }
            $tab_reclamations[] = ['state' => $state, 'reclamations' => $truncatedReclamations];
        }
        $currentUserApplications = Application::whereRelation('intervenants', 'user_id', Auth::id())->get();
        $response = ['currentUserApplications' => $currentUserApplications, 'reclamationMap' => $tab_reclamations];
        return CustomResponse::buildSuccessResponse($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'objet' => 'required',
            'description' => 'required',
            'categorie_reclamation_id' => 'required|exists:categorie_reclamations,id',
            'email' => 'required|email',
            'identifiant' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $reclamation = new Reclamation($request->all());
        $reclamation->uid = uniqid();
        // generer un nombre
        if (Reclamation::count()) {
            $numero = Reclamation::latest()->first()->numero + 1;
        } else {
            $numero = 1;
        }
        $reclamation->numero = $numero;
        $reclamation->priorite_id = Priorite::whereCode("MEDIUM")->value('id');
        // recuperer le state initial
        $stateInitial = State::whereOrdre(1)->first();
        if (!$stateInitial) {
            return CustomResponse::buildErrorResponse("Il n'ya aucun état d'entrée..., impossible de soumettre une réclamation.");
        }
        $reclamation->state_id = $stateInitial->id;
        DB::beginTransaction();
        try {
            $reclamation->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création..., merci de ressayer.");
        }
        return CustomResponse::buildSuccessResponse($reclamation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\Response
     */
    public function show(Reclamation $reclamation)
    {
        return CustomResponse::buildSuccessResponse($reclamation->load('categorieReclamation.application', 'priorite', 'user', 'state', 'comments.subs.user', 'comments.user', 'reclamationStates.state', 'reclamationStates.user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reclamation $reclamation)
    {
        $validators = Validator::make($request->all(), [
            'objet' => 'required',
            'description' => 'required',
            'categorie_reclamation_id' => 'required|exists:categorie_reclamations,id',
            'email' => 'required|email',
            'identifiant' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $userClaimAssigned = User::find($request->user_id);
        $authUser = User::find(auth()->id());
        // vérifier s'il y'a changement de state et que c'est résolu
        $newState = State::find($request->state_id);
        if ($request->state_id != $reclamation->state_id) {
            if ($newState->code == 'RESOLVED') {
                $reclamation->resolue = true;
            } else if ($newState->code == 'CLASSED') {
                $reclamation->archivee = true;
            } else {
                $reclamation->resolue = false;
                $reclamation->archivee = false;
            }
        }
        DB::beginTransaction();
        try {
            if ($userClaimAssigned != null) {
                if (($userClaimAssigned->id != $authUser->id) && ($reclamation->user_id != $request->user_id)) {
                    Mail::to($userClaimAssigned->email)->send(new NotifyUserToNewClaimAttributed($userClaimAssigned, $authUser, $reclamation));
                }
            }
            if ($reclamation->state_id != $request->state_id) {
                $reclamationState = new ReclamationState();
                $reclamationState->state_id = $reclamation->state_id;
                $reclamationState->user_id = Auth::id();
                $reclamation->reclamationStates()->save($reclamationState);
            }
            if (($request->user_id == null) && ($newState->code == "Resolving") && ($reclamation->state->code == "ENATTENTE")) {
                $reclamation->user_id = Auth::id();
                $reclamation->update($request->except('resolue', 'archivee', 'user_id'));
            } else {
                $reclamation->update($request->except('resolue', 'archivee'));
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($reclamation->load('user', 'priorite', 'state', 'comments.subs.user', 'comments.user', 'reclamationStates.state', 'reclamationStates.user'));
    }

    public function move(Request $request, Reclamation $reclamation)
    {
        $validators = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        if ($reclamation->state_id == $request->state_id) {
            return CustomResponse::buildSuccessResponse(null);
        }
        $state = State::find($request->state_id);
        if ($state->code == 'RESOLVED') {
            $resolue = 1;
        } else {
            $resolue = 0;
        }

        DB::beginTransaction();
        try {
            if (($request->user_id == null) && ($state->code == "Resolving") && ($reclamation->state->code == "ENATTENTE")) {
                $reclamation->update(['state_id' => $request->state_id, 'resolue' => $resolue, 'user_id' => Auth::id()]);
            } else {
                $reclamation->update(['state_id' => $request->state_id, 'resolue' => $resolue]);
            }
            // enregistrer le reclamation state en historique
            $reclamationState = new ReclamationState();
            $reclamationState->state_id = $request->state_id;
            $reclamationState->user_id = Auth::id();
            $reclamation->reclamationStates()->save($reclamationState);
            DB::commit();
            if ($state->code == 'RESOLVED' && $reclamation->email != auth()->user()->email) {
                Mail::to($reclamation->email)->send(new SendMailToClaimantClaimsResolved($reclamation));
            }
        } catch (\Throwable $th) {
             throw $th;
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
        }
        return CustomResponse::buildSuccessResponse($reclamation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reclamation $reclamation)
    {
        DB::beginTransaction();
        try {

            ReclamationState::whereReclamationId($reclamation->id)->delete();
            Comment::whereReclamationId($reclamation->id)->delete();
            $reclamation->deleteOrFail();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
        }
        return CustomResponse::buildSuccessResponse("Reclamation correctement supprimée");
    }

    public function paginate(Request $request)
    {
        $user = auth()->user();
        $query = Reclamation::with('priorite', 'user', 'categorieReclamation.application', 'state');
        if (!Auth::user()->is_admin) {
            $categorieReclamationIdsConnectedUser = $user->gestionnaires->pluck("categorie_reclamation_id");
            $query->whereHas('categorieReclamation', function ($query) use ($categorieReclamationIdsConnectedUser) {
                $query->whereIn('id', $categorieReclamationIdsConnectedUser);
            });
        }
        if ($request->exists('applicationId') && $request->applicationId != 0) {
            $applicationId = $request->applicationId;
            $query->whereRelation('categorieReclamation', 'application_id', $applicationId);
        }

        if ($request->exists('stateId') && $request->stateId != 0) {
            $stateId = $request->stateId;
            $query->whereStateId($stateId);
        }
        return CustomResponse::buildSuccessResponse($query->orderBy('created_at', 'desc')->paginate($request->selectedPageSizeOption));
    }

    public function getSatisfactionReclamation(Reclamation $reclamation) {
            // Vérifier si la reclamation est resolue, le déplacer à archivee
            DB::beginTransaction();
        try {
            if ($reclamation->resolue) {
                $reclamation->archivee = 1;
                $state = State::where('code', 'CLASSED')->first();
                $reclamation->state_id = $state->id;
                $reclamation->update();
                // enregistrer le reclamation state en historique
                $reclamationState = new ReclamationState();
                $reclamationState->state_id = $reclamation->state_id;
                $reclamationState->user_id = null;
                DB::commit();
            }
        } catch (\Throwable $th) {
           // throw $th;
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
        }
        return CustomResponse::buildSuccessResponse($reclamation);

    }

    public function getInsatisfactionReclamation(Reclamation $reclamation) {
        //verifier si la réclamation est resolue et la déplacer à en cours
        DB::beginTransaction();
        try {
            if ($reclamation->resolue) {
                //$reclamation->state->code == "Resolving";
                $state = State::where('code', 'Resolving')->first();
                $reclamation->state_id = $state->id;
                $reclamation->update(); 
                // enregistrer le reclamation state en historique
                $reclamationState = new ReclamationState();
                $reclamationState->state_id = $reclamation->state_id;
                $reclamationState->user_id = $reclamation->user_id;
                DB::commit();
            }
        }catch (\Throwable $th) {
                throw $th;
                DB::rollBack();
                return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
        }
        return CustomResponse::buildSuccessResponse($reclamation);

    }

    public function archiver(Reclamation $reclamation)
    {

        DB::beginTransaction();
        try {
            if ($reclamation->resolue) {
                $reclamation->archivee = 1;
                $state = State::where('code', 'CLASSED')->first();
                $reclamation->state_id = $state->id;
                $reclamation->update();
                // enregistrer le reclamation state en historique
                $reclamationState = new ReclamationState();
                $reclamationState->state_id = $reclamation->state_id;
                $reclamationState->user_id = Auth::id();
                $reclamation->reclamationStates()->save($reclamationState);
                DB::commit();
                return CustomResponse::buildSuccessResponse($reclamation->load('categorieReclamation.application', 'priorite', 'user', 'state', 'comments.subs.user', 'comments.user', 'reclamationStates', 'reclamationStates.state', 'reclamationStates.user'));
            }
            return CustomResponse::buildErrorResponse("Cette rèclamation ne peut être archivée car non résolue...");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
        }
    }
}
