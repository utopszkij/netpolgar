<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LikeController;

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

Route::get('/construction', function() { return view('construction'); } );

Route::resource('parents.teams', TeamController::class)->shallow();

Route::get('/member/list/{parent_type}/{parent}', [MemberController::class, 'index']);
Route::get('/member/{member}', [MemberController::class, 'show']);
Route::get('/member/store', [MemberController::class, 'store']);
Route::get('/member/doexit', [MemberController::class, 'doExit']);

Route::get('/like/{parentType}/{parent}', [LikeController::class, 'like']);
Route::get('/dislike/{parentType}/{parent}', [LikeController::class, 'disLike']);
Route::get('/likeinfo/{parentType}/{parent}', [LikeController::class, 'likeInfo']);


Route::get('/', function () { return view('welcome'); });
Route::get('/policy', function () { return view('policy'); });
Route::get('/policy2', function () { return view('policy2'); });
Route::get('/policy3', function () { return view('policy3'); });
Route::get('/terms', function () { return view('terms'); });
            
Route::get('/message/tree/{parentType}/{parentId}',[MessageController::class, 'tree']);
Route::get('/message/moderal/{messageId}',[MessageController::class, 'moderal']);
Route::post('/message/store',[MessageController::class, 'store']);

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

/*
* univerzális vue aktivizáló route
*/
Route::get('run/{p1?}/{p2?}/{p3?}/{p4?}/{p5?}/{p6?}', function () {
	include 'vueRun.php';	 
	 });

