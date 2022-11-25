<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationProfilController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieReclamationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GestionnaireController;
use App\Http\Controllers\IntervenantController;
use App\Http\Controllers\PrioriteController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home', function () {
    return redirect()->to('home');
});

Route::get('reclamation/satisfaction/{reclamation:uid}', [ReclamationController::class, 'getSatisfactionReclamation']);
Route::get('reclamation/insatisfaction/{reclamation:uid}', [ReclamationController::class, 'getInsatisfactionReclamation']);


Route::get('/comment/reclameur/{comment:uid}', [CommentController::class, 'getComment']);

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
Route::post('auth/reset-password-account', [AuthController::class, 'checkEmailAdressAndSendMailForResetPassword']);
Route::post('auth/check-token', [AuthController::class, 'checkTokenAndgetEmailFromToken']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::get('application/applications', [ApplicationController::class, 'getAuthUserApplications']);
Route::resource('state', StateController::class);
Route::resource('profil', ProfilController::class);
Route::put('application/update-logo/{application:id}', [ApplicationController::class, 'updateLogo']);
Route::resource('application', ApplicationController::class);
Route::get('/auth/account', [AuthController::class, 'getConnectedUser']);
Route::put('user/update-photo/{user:id}', [UserController::class, 'updatePhoto']);
Route::put('user/attribute-new-password/{user:id}', [UserController::class, 'attributeNewPassword']);
Route::put('auth/change-password', [AuthController::class, 'changePassword']);
Route::get('user/reclamations', [UserController::class, 'getUserForSameCategorieReclamation']);
Route::get('categoriereclamation/categories-reclamation', [CategorieReclamationController::class, 'getAuthUserCategorieReclamations']);
Route::resource('user', UserController::class);
Route::resource('intervenant', IntervenantController::class, [
    'only' => ['store', 'destroy']
]);
Route::resource('applicationprofil', ApplicationProfilController::class, [
    'only' => ['store', 'destroy']
]);
Route::resource('categoriereclamation', CategorieReclamationController::class, [
    'except' => ['index']
]);
Route::resource('gestionnaire', GestionnaireController::class, [
    'only' => ['store', 'destroy']
]);
Route::get('user/responsables-reclamation/{reclamation:id}', [UserController::class, 'getResponsablesReclamation']);
Route::post('reclamation/paginate', [ReclamationController::class, 'paginate']);
Route::post('reclamation/dashboard', [ReclamationController::class, 'dashboard']);
Route::put('reclamation/move/{reclamation}', [ReclamationController::class, 'move']);
Route::put('reclamation/archive/{reclamation}', [ReclamationController::class, 'archiver']);
Route::resource('reclamation', ReclamationController::class);
Route::resource('priorite', PrioriteController::class);
Route::resource('comment', CommentController::class);
