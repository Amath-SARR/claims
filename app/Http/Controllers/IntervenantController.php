<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\Gestionnaire;
use App\Models\Intervenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IntervenantController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->only(['destroy', 'store']);
        $this->middleware('gestionnaire')->except(['destroy', 'store']);
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
            'application_id' => 'required|exists:applications,id',
            'user_id' => Rule::unique('intervenants')->where(function ($query) use ($request) {
                return $query->whereUserId($request->user_id)
                    ->whereApplicationId($request->application_id);
            })
        ], ['user_id.unique' => 'Cet utilisateur est déja ajouté...']);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        // store object
        $intervenant = new Intervenant($request->all());
        DB::beginTransaction();
        try {
            $intervenant->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création de intervenant...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($intervenant->load('user', 'application.categorieReclamations'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Intervenant  $intervenant
     * @return \Illuminate\Http\Response
     */
    public function show(Intervenant $intervenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Intervenant  $intervenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Intervenant $intervenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Intervenant  $intervenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Intervenant $intervenant)
    {
        DB::beginTransaction();
        try {
            $intervenant->deleteOrFail();
            Gestionnaire::whereUserId($intervenant->user_id)
                ->whereHas("categorieReclamation", function ($query) use ($intervenant) {
                    $query->whereApplicationId($intervenant->application_id);
                })->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification de intervenant...");
        }
        return CustomResponse::buildSuccessResponse($intervenant);
    }
}
