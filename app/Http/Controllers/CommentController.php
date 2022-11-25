<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Mail\SendCommentToClaimant;
use App\Mail\SendMailCommentToDestined;
use App\Mail\SendMailCommentToResponsibleTask;
use App\Mail\TestMail;
use App\Models\Comment;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('gestionnaire')->only('destroy');
        $this->middleware('admin')->except(['store', 'destroy', 'getComment']);
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
            'reclamation_id' => 'required|exists:reclamations,id',
            'commentaire' => 'required',
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        // store object
        $comment = new Comment($request->all());
        $reclamation = Reclamation::find($comment->reclamation_id);
        if (!Auth::check() && $request->comment_id) {
            $comment->user_id = null;
            $oldComment = Comment::where('id', $request->comment_id)->with('user')->get();
        } else {
            $comment->user_id = Auth::id();
        }
        if ($request->forReclameur) {
            $comment->for_reclameur = $request->forReclameur;
        } else {
            $comment->for_reclameur = false;
        }
        $comment->uid = uniqid();
        DB::beginTransaction();
        try {
            $comment->save();
            DB::commit();
            /* if (!Auth::check()) {
                if ($comment->comment_id == null) {
                    Mail::to($reclamation->user->email)->send(new SendMailCommentToDestined($comment));
                } else {
                    if ($reclamation->user_id == null) {
                        Mail::to($oldComment[0]->user->email)->send(new SendMailCommentToDestined($comment));
                    } else {
                            if ($oldComment[0]->user_id != $reclamation->user_id) {
                                Mail::to($oldComment[0]->user->email)->send(new SendMailCommentToDestined($comment));
                                Mail::to($reclamation->user->email)->send(new SendMailCommentToDestined($comment));
                            } else {
                                Mail::to($reclamation->user->email)->send(new SendMailCommentToDestined($comment));
                            }
                        }
                }
            } else {
                if ($comment->comment_id == null) {
                    //Teste pour envoyer un mail au réclameur
                    if ($comment->for_reclameur) {
                        Mail::to($reclamation->email)->send(new SendCommentToClaimant($comment));
                    }
                    //vérifier si cet tâche a été assigné
                    if ($reclamation->user_id == null) {
                        return CustomResponse::buildSuccessResponse($comment);
                    } else {
                        if ($reclamation->user_id != Auth::id()) {
                            Mail::to($reclamation->user->email)->send(new SendMailCommentToDestined($comment));
                        }
                    }
                } else {
                    //Teste pour envoyer un mail au réclameur
                    if ($comment->comment->for_reclameur && $comment->user_id) {
                        Mail::to($reclamation->email)->send(new SendCommentToClaimant($comment));
                    }
                    //vérifier si cet tâche a été assigné
                    if ($reclamation->user_id == null) {
                        if ($comment->comment->user_id == Auth::id()) {
                            return CustomResponse::buildSuccessResponse($comment);
                        } else {
                            Mail::to($comment->comment->user->email)->send(new SendMailCommentToDestined($comment));
                        }
                    } else {
                        if ($comment->comment->user_id != Auth::id()) {
                            Mail::to($comment->comment->user->email)->send(new SendMailCommentToDestined($comment));
                            if (
                                $comment->user_id != $comment->reclamation->user_id &&
                                $comment->comment->user_id != $comment->reclamation->user_id) {
                                    Mail::to($reclamation->user->email)->send(new SendMailCommentToResponsibleTask($comment));
                            }
                        }
                        if (
                            $comment->comment->user_id == Auth::id() && $comment->user_id != $comment->reclamation->user_id &&
                            $comment->comment->user_id != $comment->reclamation->user_id) {
                                Mail::to($reclamation->user->email)->send(new SendMailCommentToResponsibleTask($comment));
                        }
                    }
                }
            } */
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la création...");
        }
        return CustomResponse::buildSuccessResponse($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        DB::beginTransaction();
        try {
            Comment::whereCommentId($comment->id)->delete();
            $comment->deleteOrFail();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification...");
            //throw $th;
        }
        return CustomResponse::buildSuccessResponse($comment);
    }

    /**
     * Find the specified resource.
     * @author EL Hadji Amath SARR
     * @link https://uidt.atlassian.net/browse/CLAIMS-152
     * @since 24/03/2022
     * @return \Illuminate\Http\Response
     */
    public function getComment(Comment $comment)
    {
        $subs = Comment::whereCommentId($comment->id)->with('user')->latest()->get();
        $comment = Comment::whereId($comment->id)->with('user', 'reclamation')->first();
        $data = [
            'comment' => $comment,
            'subs' => $subs
        ];
        return CustomResponse::buildSuccessResponse($data);
    }
}
