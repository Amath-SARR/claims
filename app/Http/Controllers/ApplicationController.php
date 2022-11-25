<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Custom\FileHelper;
use App\Models\Application;
use App\Models\ApplicationProfil;
use App\Models\CategorieReclamation;
use App\Models\Gestionnaire;
use App\Models\Intervenant;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('gestionnaire')->only(['getAuthUserApplications','show']);
        $this->middleware('admin')->except(['getAuthUserApplications', 'index','show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $applications = Application::with('categorieReclamations')->get();
        return CustomResponse::buildSuccessResponse($applications);
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
            'nom' => 'required|unique:applications,nom|min:3',
            'code' => 'required|unique:applications,code',
            'presentation' => 'required',
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $application = new Application($request->all());
        DB::beginTransaction();
        try {
            // vérifier s'il y'a logo et uploader le logo
            if ($request->exists('logo')) {
                $file = FileHelper::getFileFromBase64($request->logo);
                $extension = $file->guessClientExtension();
                $filename = $request->code . '_' . uniqid() . '.' . $extension;
                $application->logo = $filename;
                $file->storeAs('application/logos', $filename);
            }
            $application->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création du application...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($application);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        return CustomResponse::buildSuccessResponse($application->load('intervenants.user', 'applicationProfils.profil', 'categorieReclamations.gestionnaires.user', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        $validators = Validator::make($request->all(), [
            'nom' => 'required|unique:applications,nom,' . $application->id,
            'code' => 'required|unique:applications,code,' . $application->id,
            'presentation' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $application->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du application...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($application);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Application $application)
    { 
        // Employee::with('employees.employee_locations')->find(1);
        // Application::find(1)->categorieReclamation();           
        DB::beginTransaction();
        try {
            
            $categorieReclamations = CategorieReclamation:: where('application_id', $application->id)->get();
            if(count($categorieReclamations) != 0) {
                return CustomResponse::buildErrorResponse("Suppression impossible! Cette application contient des catégories de réclamations. Veuillez d'abord les supprimer.");
            }
            else {
                $application->intervenants()->delete();
                $application->applicationProfils()->delete();
                $application->deleteOrFail();
                if ($application->logo ) {
                    // supprimer le logo si la suppression reussie
                    Storage::delete('application/logos/' . $application->logo);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur s'est produite lors du traitement");
        }
        return CustomResponse::buildSuccessResponse("Application supprimée!");
    }


    public function updateLogo(Request $request, Application $application)
    {
        $validators = Validator::make($request->all(), [
            'logo' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            if ($application->logo) {
                Storage::delete('application/logos/' . $application->logo);
            }
            $logoFile = FileHelper::getFileFromBase64($request->logo);
            $extension = $logoFile->guessClientExtension();
            $filename = Str::lower($request->code) . '_' . uniqid() . '.' . $extension;
            $logoFile->storeAs('application/logos', $filename);
            $application->logo = $filename;
            $application->updateOrFail();
            DB::commit();
            return CustomResponse::buildSuccessResponse($application);
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur inattendue s'est produite pendant la modification du logo...");
        }
    }

    public function getAuthUserApplications()
    {
        if (Auth::user()->is_admin) {
            $applications = Application::all()->load('categorieReclamations');
        } else {
            $applications = Auth::user()->applications->load('categorieReclamations');
        }

        return CustomResponse::buildSuccessResponse($applications);
    }
}
