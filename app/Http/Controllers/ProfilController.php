<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->except(['index','show']);
        $this->middleware(['gestionnaire'])->only('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profils = Profil::with('applications.categorieReclamations')->get();
        return CustomResponse::buildSuccessResponse($profils);
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
            'nom' => 'required|unique:profils,nom|min:3',
            'code' => 'required|unique:profils,code',
            'ordre' => 'required|unique:profils,ordre',
            'description' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $profil = new Profil($request->all());
        DB::beginTransaction();
        try {
            $profil->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création du profil...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($profil);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function show(Profil $profil)
    {
        return CustomResponse::buildSuccessResponse($profil->load('applicationProfils.application'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profil $profil)
    {
        $validators = Validator::make($request->all(), [
            'nom' => 'required|unique:profils,nom,' . $profil->id,
            'code' => 'required|unique:profils,code,' . $profil->id,
            'ordre' => 'required|unique:profils,ordre,' . $profil->id,
            'description' => 'required',
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $profil->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du profil...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($profil);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profil $profil)
    {
        DB::beginTransaction();
        try {
            $profil->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du profil...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($profil);
    }
}
