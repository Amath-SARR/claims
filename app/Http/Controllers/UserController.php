<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Custom\FileHelper;
use App\Mail\NewAccountEmail;
use App\Mail\SendMailToNewUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use A6digital\Image\DefaultProfileImage;
use App\Mail\NotifyUserToNewPasswordAttributed;
use App\Mail\SendUserRecapMail;
use App\Models\Application;
use App\Models\CategorieReclamation;
use App\Models\Reclamation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->except(['getUserForSameCategorieReclamation', 'getResponsablesReclamation', 'updatePhoto','show','index']);
        $this->middleware('gestionnaire')->only(['getUserForSameCategorieReclamation', 'getResponsablesReclamation','show','index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return CustomResponse::buildSuccessResponse($users);
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
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'type' => 'required',
            'poste' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $user = new User($request->all());
        DB::beginTransaction();
        try {
            // vérifier s'il y'a photo et uploader le photo
            if ($request->exists('photo')) {
                $file = FileHelper::getFileFromBase64($request->photo);
                $extension = $file->guessClientExtension();
                $filename = $request->email . '_' . uniqid() . '.' . $extension;
                $user->photo = $filename;
                $file->storeAs('user/photos', $filename);
            } else {
                $randomBgColor = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
                $img = DefaultProfileImage::create($request->name, 256, $randomBgColor, '#FFF');
                $filename = $request->email . '_' . uniqid() . '.png';
                $user->photo = $filename;
                Storage::put("user/photos/" . $filename, $img->encode());
            }
            // generer password default
            $plainPassword = Str::random(8);
            $user->password = Hash::make($plainPassword);
            $user->enabled = true;
            $user->save();
            DB::commit();
            // envoyer un email à l'utilisateur pour l'informer
            Mail::to($request->email)->send(new NewAccountEmail($user, $plainPassword));
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création du user...");
        }
        return CustomResponse::buildSuccessResponse($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return CustomResponse::buildSuccessResponse($user->load('intervenants.application.categorieReclamations', 'gestionnaires.categorieReclamation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'type' => 'required',
            'poste' => 'required'
        ]);

        // return $request;
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $oldMail = $user->email;
            $user->update($request->all());
            if ($oldMail != $user->email) {
                Mail::to($user->email)->send(new SendMailToNewUserMail($oldMail, $user));
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du user...");
        }
        return CustomResponse::buildSuccessResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();

        try {
            $user->deleteOrFail();
            if ($user->photo) {
                // supprimer le photo si la suppression reussie
                Storage::delete('user/photos/' . $user->photo);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la suppresion...");
        }
        return CustomResponse::buildSuccessResponse($user);
    }

    public function attributeNewPassword(Request $request, User $user)
    {
        $validators = Validator::make($request->all(), [
            'newPassword' => 'required|min:6',
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $newPassword = $request->get('newPassword');
            $user->password = Hash::make($newPassword);
            $user->update();
            DB::commit();
            Mail::to($user->email)->send(new NotifyUserToNewPasswordAttributed($user, $newPassword));
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du mot de passe de l'utilisateur...");
        }
        return CustomResponse::buildSuccessResponse("Nouveau mot de passe correctement attribuée");
    }

    public function updatePhoto(Request $request, User $user)
    {
        $validators = Validator::make($request->all(), [
            'photo' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            if ($user->photo) {
                Storage::delete('user/photos/' . $user->photo);
            }
            $photoFile = FileHelper::getFileFromBase64($request->photo);
            $extension = $photoFile->guessClientExtension();
            $filename = Str::lower($request->code) . '_' . uniqid() . '.' . $extension;
            $photoFile->storeAs('user/photos', $filename);
            $user->photo = $filename;
            $user->updateOrFail();
            DB::commit();
            return CustomResponse::buildSuccessResponse($user);
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur inattendue s'est produite pendant la modification de la photo...");
        }
    }

    public function getUserForSameCategorieReclamation()
    {
        if (Auth::user()->is_admin) {
            $users = User::whereHas('reclamations')->get();
        } else {
            $user = auth()->user();
            $categorieReclamationIdsConnectedUser = $user->gestionnaires->pluck("categorie_reclamation_id");
            $users = User::whereHas('gestionnaires', function ($query) use ($categorieReclamationIdsConnectedUser) {
                $query->whereIn('categorie_reclamation_id', $categorieReclamationIdsConnectedUser);
            })->whereHas('reclamations')->get();
        }
        return CustomResponse::buildSuccessResponse($users);
    }

    public function getResponsablesReclamation(Reclamation $reclamation)
    {
        $responsables = User::whereHas('gestionnaires', function ($query) use ($reclamation) {
            $query->whereCategorieReclamationId($reclamation->categorieReclamation->id);
        })->get();
        return CustomResponse::buildSuccessResponse($responsables);
    }

}
