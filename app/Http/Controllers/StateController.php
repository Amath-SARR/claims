<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CustomResponse::buildSuccessResponse(State::orderBy('ordre')->get());
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
            'nom' => 'required|unique:states,nom|min:3',
            'code' => 'required|unique:states,code',
            'ordre' => 'required|unique:states,ordre',
            'class' => 'required|unique:states,class'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $state = new State($request->all());
        DB::beginTransaction();
        try {
            $state->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création du state...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($state);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        return CustomResponse::buildSuccessResponse($state);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $validators = Validator::make($request->all(), [
            'nom' => 'required|unique:states,nom,' . $state->id,
            'code' => 'required|unique:states,code,' . $state->id,
            'ordre' => 'required|unique:states,ordre,' . $state->id,
            'class' => 'required|unique:states,class,' . $state->id,
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        DB::beginTransaction();
        try {
            $state->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du state...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($state);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        DB::beginTransaction();
        try {
            $state->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du state...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($state);
    }
}
