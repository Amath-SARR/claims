<?php


namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\CategorieReclamation;
use App\Models\Gestionnaire;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategorieReclamationController extends Controller
{

    public function __construct()
    {
        $this->middleware('gestionnaire')->only(['show', 'index', 'getAuthUserCategorieReclamations']);
        $this->middleware('admin')->except(['show', 'index', 'getAuthUserCategorieReclamations']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'nom' => 'required',
            'description' => 'required',
            'guide' => 'required',
            'application_id' => 'exists:applications,id'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $categoriereclamation = new CategorieReclamation($request->all());
        DB::beginTransaction();
        try {
            $categoriereclamation->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création du application...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($categoriereclamation->load('gestionnaires.user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategorieReclamation  $categoriereclamation
     * @return \Illuminate\Http\Response
     */
    public function show(CategorieReclamation $categoriereclamation)
    {
        return CustomResponse::buildSuccessResponse($categoriereclamation->load('intervenants.user', 'applicationProfils.profil'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CategorieReclamation  $categoriereclamation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategorieReclamation $categoriereclamation)
    {
        $validators = Validator::make($request->all(), [
            'nom' => 'required',
            'description' => 'required',
            'guide' => 'required',
            'application_id' => 'exists:applications,id'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $categoriereclamation->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du application...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($categoriereclamation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategorieReclamation  $categoriereclamation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategorieReclamation $categoriereclamation)
    {
        DB::beginTransaction();
        try {
            $categoriereclamation->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($categoriereclamation);
    }

    public function getAuthUserCategorieReclamations()
    {
        if (Auth::user()->is_admin) {
            $categoriesReclamation = CategorieReclamation::all();
        } else {
            $categoriesReclamation = Auth::user()->categorieReclamations;
        }
        return CustomResponse::buildSuccessResponse($categoriesReclamation);
    }
}
