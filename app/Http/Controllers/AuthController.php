<?php

namespace App\Http\Controllers;

use App\Custom\CustomResponse;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('gestionnaire')->except(['login', 'logout', 'resetPassword', 'checkTokenAndgetEmailFromToken', 'checkEmailAdressAndSendMailForResetPassword','getConnectedUser']);
        $this->middleware('auth')->only(['getConnectedUser']);
    }
    /**
     * Login of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        if (!User::whereEmail($request->email)->value('enabled')) {
            return CustomResponse::buildErrorResponse("Ce compte a été désactivé \n Veuillez contacter un administrateur pour plus d'informations");
        } else {
            if (Auth::attempt($request->only('email', 'password'))) {
                User::find(Auth::id())->update(['last_login_at' => now()]);
                return CustomResponse::buildSuccessResponse(Auth::user());
            }
        }
        return CustomResponse::buildErrorResponse("Nous n'avons pas pu vous identifier...");
    }

    public function logout()
    {
        if (Auth::check()) {
            User::find(Auth::id())->update(['last_login_at' => now()]);
            Auth::logout();
            return CustomResponse::buildSuccessResponse("Utilisateur déconnecté...");
        }
        return CustomResponse::buildErrorResponse("Erreur survenue lors du login");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetPasswordToken(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function getCurrentUser()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function verifyTokenForPasswordReset(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function setNewPasswordAfterResetConfirmation(User $user)
    {
        //
    }

    /**
     * Find the specified resource.
     * @author EL Hadji Amath SARR
     * @link https://uidt.atlassian.net/browse/CLAIMS-32
     * @since 01/03/2022
     * @return \Illuminate\Http\Response
     */
    public function getConnectedUser()
    {
        return CustomResponse::buildSuccessResponse(auth()->user());
    }

    /**
     * Update password the specified resource.
     * @author EL Hadji Amath SARR
     * @link https://uidt.atlassian.net/browse/CLAIMS-31
     * @since 02/03/2022
     * @param  \App\Models\Request $request
     * @return \Illuminate\Http\Promise
     */
    public function changePassword(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required_with:newPassword|same:newPassword|min:6',
            'currentPassword' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $user = User::find(auth()->id());
        if (Hash::check($request->get('currentPassword'), $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->update();
        } else {
            return CustomResponse::buildErrorResponse("Le mot de passe saisi est incorrect");
        }
        return CustomResponse::buildSuccessResponse("Votre mot de passe a été changé avec succés");
    }

    public function checkEmailAdressAndSendMailForResetPassword(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        $user = User::whereEmail($request->email)->first();
        if ($user == null) {
            return CustomResponse::buildErrorResponse("Il n'existe pas de compte associé à l'adresse email " . $request->email);
        }
        try {
            DB::beginTransaction();
            $passwordReset = PasswordReset::create(
                [
                    'email' => $user->email,
                    'token' => uniqid()
                ]
            );
            DB::commit();
            Mail::to($user->email)->send(new PasswordResetMail($user, $passwordReset));
        } catch (\Throwable $th) {
            DB::rollBack();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la réinitialisation de votre mot de passe...");
        }

        return CustomResponse::buildSuccessResponse("Veuillez vérifier votre boite mail et suivre les différentes instructions pour réinitialiser votre mot de passe");
    }

    public function checkTokenAndgetEmailFromToken(Request $request)
    {
        try {
            $passwordReset = PasswordReset::whereToken($request->token)->whereEmail($request->email)->first();
            if ($passwordReset != null) {
                return CustomResponse::buildSuccessResponse($request->email);
            }
            return CustomResponse::buildErrorResponse("Ce lien de réinitialisation est invalide");
        } catch (\Throwable $th) {
            return CustomResponse::buildErrorResponse("Une erreur est survenue...");
        }
    }

    public function resetPassword(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'newPassword' => 'required|min:6'
        ]);
        if ($validators->fails()) {
            return CustomResponse::buildValidationErrorResponse($validators->errors());
        }
        try {
            DB::beginTransaction();
            $user = User::whereEmail($request->email)->first();
            $user->password = bcrypt($request->newPassword);
            $user->update();
            PasswordReset::find($request->email)->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return CustomResponse::buildErrorResponse("Une erreur est survenue lors de la modification du mot de passe...");
        }
        return CustomResponse::buildSuccessResponse("Votre mot de passe a correctement été modifiée");
    }
}
