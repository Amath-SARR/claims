<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\Gestionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GestionnaireController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->only(['store', 'destroy']);
        $this->middleware('gestionnaire')->except(['store', 'destroy']);
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
            'user_id' => 'required|exists:users,id',
            'categorie_reclamation_id' => 'required|exists:categorie_reclamations,id',
            'user_id' => Rule::unique('gestionnaires')->where(function ($query) use ($request) {
                return $query->whereUserId($request->user_id)
                    ->whereCategorieReclamationId($request->categorie_reclamation_id);
            })
        ], ['user_id.unique' => 'Cet utilisateur est déja ajouté...']);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        // store object
        $gestionnaire = new Gestionnaire($request->all());
        DB::beginTransaction();
        try {
            $gestionnaire->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($gestionnaire->load('user', 'categorieReclamation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gestionnaire  $gestionnaire
     * @return \Illuminate\Http\Response
     */
    public function show(Gestionnaire $gestionnaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gestionnaire  $gestionnaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gestionnaire $gestionnaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gestionnaire  $gestionnaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gestionnaire $gestionnaire)
    {
        DB::beginTransaction();
        try {
            $gestionnaire->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification de gestionnaire...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($gestionnaire);
    }
}
