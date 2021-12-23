<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProjectController;

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

// members
Route::get('/member/list/{parent_type}/{parent}', [MemberController::class, 'index']);
Route::get('/member/store', [MemberController::class, 'store']);
Route::get('/member/{member}', [MemberController::class, 'show']);
Route::get('/member/doexit', [MemberController::class, 'doExit']);
Route::get('/member/user/{userId}', [MemberController::class, 'user']);
// like
Route::get('/like/{parentType}/{parent}', [LikeController::class, 'like']);
Route::get('/dislike/{parentType}/{parent}', [LikeController::class, 'disLike']);
Route::get('/likeinfo/{parentType}/{parent}', [LikeController::class, 'likeInfo']);
// policy, impressum
Route::get('/', function () { return view('welcome'); });
Route::get('/policy', function () { return view('policy'); });
Route::get('/policy2', function () { return view('policy2'); });
Route::get('/policy3', function () { return view('policy3'); });
Route::get('/terms', function () { return view('terms'); });
Route::get('/impressum', function () { return view('impressum'); });
// message            
Route::get('/message/tree/{parentType}/{parentId}/{offset?}',[MessageController::class, 'tree']);
Route::get('/message/moderal/{messageId}',[MessageController::class, 'moderal']);
Route::post('/message/store',[MessageController::class, 'store']);
Route::get('/message/protest/{messageId}',[MessageController::class, 'protest']);
Route::post('/message/saveprotest',[MessageController::class, 'saveprotest']);
Route::get('/message/list/{parentType}/{parentId}/{replyTo}/{offset?}',[MessageController::class, 'list']);
// poll
Route::get('/{parentType}/{parent}/{statuses}/polls',[PollController::class, 'index']);
Route::get('/{parentType}/{parent}/{statuses}/polls/create',[PollController::class, 'create']);
Route::get('/polls/{poll}',[PollController::class, 'show']);
Route::get('/polls/{poll}/edit',[PollController::class, 'edit']);
Route::post('/polls',[PollController::class, 'store']);
Route::post('/polls/{poll}',[PollController::class, 'update']);
// option
Route::get('/{poll}/options/create',[OptionController::class, 'create']);
Route::get('/options/{option}/edit',[OptionController::class, 'edit']);
Route::post('/options',[OptionController::class, 'store']);
Route::post('/options/{option}',[OptionController::class, 'update']);
// vote
Route::get('/{poll}/votes/create',[VoteController::class, 'create']);
Route::get('{poll}/votes/getform',[VoteController::class, 'getform']);
Route::post('/votes/show',[VoteController::class, 'show']);
Route::get('{poll}/votes',[VoteController::class, 'list']);
Route::post('/votes',[VoteController::class, 'store']);
Route::get('{poll}/votes/csv',[VoteController::class, 'csv']);
// projects
Route::get('/{team}/projects',[ProjectController::class, 'index']);
Route::get('/{team}/projects/create',[ProjectController::class, 'create']);
Route::get('/projects/{project}',[ProjectController::class, 'show']);
Route::get('/projects/{project}/edit',[ProjectController::class, 'edit']);
Route::post('/projects',[ProjectController::class, 'store']);
Route::post('/projects/{project}',[ProjectController::class, 'update']);

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

