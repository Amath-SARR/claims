<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\ApplicationProfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApplicationProfilController extends Controller
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
            'profil_id' => 'required|exists:profils,id',
            'application_id' => 'required|exists:applications,id',
            'profil_id' => Rule::unique('application_profils')->where(function ($query) use ($request) {
                return $query->whereProfilId($request->profil_id)
                    ->whereApplicationId($request->application_id);
            })
        ], ['profil_id.unique' => 'Ce profil est déja associé...']);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        // store object
        $applicationprofil = new ApplicationProfil($request->all());
        DB::beginTransaction();
        try {
            $applicationprofil->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de l'ajout du profil...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($applicationprofil->load('profil', 'application'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ApplicationProfil  $applicationprofil
     * @return \Illuminate\Http\Response
     */
    public function show(ApplicationProfil $applicationprofil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ApplicationProfil  $applicationprofil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApplicationProfil $applicationprofil)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ApplicationProfil  $applicationprofil
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApplicationProfil $applicationprofil)
    {
        DB::beginTransaction();
        try {
            $applicationprofil->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification de applic$applicationprofil...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($applicationprofil);
    }
}
