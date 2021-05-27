<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\MessagesController;

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

Route::get('/', function () { return view('welcome'); });
Route::get('/policy', function () { return view('policy'); });
Route::get('/policy2', function () { return view('policy2'); });
Route::get('/policy3', function () { return view('policy3'); });
Route::get('/terms', function () { return view('terms'); });
            
Route::get('/groups/{parent_id}/{member_id}/{admin_id}', [GroupsController::class, 'list']);
Route::get('/group/form/{id}/{parent_id}', [GroupsController::class, 'form']);
Route::get('/group/show/{id}', [GroupsController::class, 'show']);
Route::post('/group/save', [GroupsController::class, 'save']);
Route::post('/group/delete/{id}', [GroupsController::class, 'delete']);

Route::get('/like/{parentType}/{id}/{likeType}', [MessagesController::class, 'like']);

Route::get('/messages/{parentType}/{id}',[MessagesController::class, 'list']);
Route::get('/messageadd/{parentType}/{parentId}/{txt}',[MessagesController::class, 'add']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


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
Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);
Route::get('auth/facebook/callback', [SocialController::class, 'loginWithFacebook']);
Route::get('auth/google', [SocialController::class, 'googleRedirect']);
Route::get('auth/google/callback', [SocialController::class, 'loginWithGoogle']);
Route::get('auth/github', [SocialController::class, 'githubRedirect']);
Route::get('auth/github/callback', [SocialController::class, 'loginWithGithub']);
