<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Custom\FileHelper;
use App\Models\Priorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrioriteController extends Controller
{

    public function __construct()
    {
        $this->middleware('gestionnaire')->only(['show', 'index']);
        $this->middleware('admin')->except(['show', 'index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priorites = Priorite::all();
        return CustomResponse::buildSuccessResponse($priorites);
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
            'nom' => 'required|unique:priorites,nom|min:3',
            'code' => 'required|unique:priorites,code',
            'icone' => 'required|unique:priorites,icone',
            'class' => 'required|unique:priorites,class',
            'level' => 'required|unique:priorites,level',
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $priorite = new Priorite($request->all());
        DB::beginTransaction();
        try {
            $priorite->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création de la  priorite...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($priorite);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Priorite  $priorite
     * @return \Illuminate\Http\Response
     */
    public function show(Priorite $priorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Priorite  $priorite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Priorite $priorite)
    {
        $validators = Validator::make($request->all(), [
            'nom' => 'required|unique:priorites,nom,' . $priorite->id,
            'code' => 'required|unique:priorites,code,' . $priorite->id,
            'icone' => 'required|unique:priorites,icone,' . $priorite->id,
            'class' => 'required|unique:priorites,class,' . $priorite->id,
            'level' => 'required|unique:priorites,level,' . $priorite->id,
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $priorite->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du priorité...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($priorite);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Priorite  $priorite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Priorite $priorite)
    {
        DB::beginTransaction();
        try {
            $priorite->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la suppression du priorité...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($priorite);
    }
}
