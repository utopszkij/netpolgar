<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\Admin;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\FileController;

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

// team
Route::resource('parents.teams', TeamController::class)->shallow();
Route::get('/team/tree', [TeamController::class, 'tree']);
Route::get('/users/{userId}/teams', [TeamController::class, 'listByUser']);

// members
Route::get('/member/doexit', [MemberController::class, 'doExit'])
	->middleware('auth');
Route::get('/member/list/{parent_type}/{parent}', [MemberController::class, 'index']);
Route::get('/member/store', [MemberController::class, 'store']);
Route::get('/member/{member}', [MemberController::class, 'show']);
Route::get('/member/user/{userId}', [MemberController::class, 'user']);
// like
Route::get('/like/{parentType}/{parent}', [LikeController::class, 'like'])
	->middleware('auth');
Route::get('/dislike/{parentType}/{parent}', [LikeController::class, 'disLike'])
	->middleware('auth');
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
Route::get('/message/moderal/{messageId}',[MessageController::class, 'moderal'])
	->middleware('auth');
Route::post('/message/store',[MessageController::class, 'store']);
Route::get('/message/protest/{messageId}',[MessageController::class, 'protest']);
Route::post('/message/saveprotest',[MessageController::class, 'saveprotest']);
Route::get('/message/list/{parentType}/{parentId}/{replyTo}/{offset?}',[MessageController::class, 'list']);
// poll
Route::get('/{parentType}/{parent}/{statuses}/polls',[PollController::class, 'index']);
Route::get('/{parentType}/{parent}/{statuses}/polls/create',[PollController::class, 'create'])
	->middleware('auth');
Route::get('/polls/{poll}',[PollController::class, 'show']);
Route::get('/polls/{poll}/edit',[PollController::class, 'edit'])
	->middleware('auth');
Route::post('/polls',[PollController::class, 'store']);
Route::post('/polls/{poll}',[PollController::class, 'update']);
// option
Route::get('/{poll}/options/create',[OptionController::class, 'create'])
	->middleware('auth');
Route::get('/options/{option}/edit',[OptionController::class, 'edit'])
	->middleware('auth');
Route::post('/options',[OptionController::class, 'store']);
Route::post('/options/{option}',[OptionController::class, 'update']);
// vote
Route::get('/{poll}/votes/create',[VoteController::class, 'create'])
	->middleware('auth');
Route::get('{poll}/votes/getform',[VoteController::class, 'getform'])
	->middleware('auth');
Route::post('/votes/show',[VoteController::class, 'show']);
Route::get('{poll}/votes',[VoteController::class, 'list']);
Route::post('/votes',[VoteController::class, 'store']);
Route::get('{poll}/votes/csv',[VoteController::class, 'csv']);
// projects
Route::get('/{team}/projects',[ProjectController::class, 'index']);
Route::get('/projectsbyuser/{userId}',[ProjectController::class, 'listByUser']);
Route::get('/{team}/projects/create',[ProjectController::class, 'create'])
	->middleware('auth');
Route::get('/projects/{project}',[ProjectController::class, 'show']);
Route::get('/projects/{project}/edit',[ProjectController::class, 'edit'])
	->middleware('auth');
Route::post('/projects',[ProjectController::class, 'store']);
Route::post('/projects/{project}',[ProjectController::class, 'update']);
// tasks
Route::get('/tasks/dragsave',[TaskController::class, 'dragsave']);
Route::get('/{project}/tasks/create',[TaskController::class, 'create'])
	->middleware('auth');
Route::get('/tasks/{task}',[TaskController::class, 'show']);
Route::get('/tasks/{task}/edit',[TaskController::class, 'edit'])
	->middleware('auth');
Route::get('/tasks/{task}/delete',[TaskController::class, 'destroy'])
    ->middleware('auth');
Route::post('/tasks',[TaskController::class, 'store']);
Route::post('/tasks/{task}',[TaskController::class, 'update']);
// file
Route::get('/file/list/{parentType}/{parentId}/{userId}',[FileController::class, 'index']);
Route::get('/file/add/{parentType}/{parentId}/{userId}',[FileController::class, 'create'])
    ->middleware('auth');
Route::get('/file/show/{id}',[FileController::class, 'show']);
Route::get('/file/edit/{id}',[FileController::class, 'edit'])
    ->middleware('auth');
Route::get('/file/delete/{id}',[FileController::class, 'delete'])
    ->middleware('auth');
Route::get('/file/download/{id}',[FileController::class, 'download'])
    ->middleware('auth');
Route::post('/file/store',[FileController::class, 'store']);
Route::post('/file/update',[FileController::class, 'update']);


/*----------------------------------
*          WEB áruház
* ----------------------------------
* táblák: products   (id,name, description, avatar, status, price, unit, stock)
*         productcats   (id, product_id, category_id)
*         productadd (id, product_id, quantity, description)
*         orders     (id, description, address, shipping, status, confirmInfo)
*            status: open|ordered|inwork|ok|success|notok|canceled 
*         orderitems (id, product_id, quantity, status, confirmInfo)
*            status: open|ordered|ok|notok|success1|success2
*         evaluations (id, user_id, product_id, value)
*			 currentaccounts (id, user_id, balance)
*   - like/dislike van a product-hoz
*   - messages van a product-hoz és az order -hez
*   - az order/list -ről üzenetet lehet küldeni a megrendelőnek
* 
*   megrendelés visszaigazolás folyamata:
*   1. team adminok a orderitems -ben a hozzájukt tartozó tételeket
*      igazolják vissza ('ok'|'notok') és irnak a confirmInfo -ba
*   2. ha az összes orderitem -ben 'ok' akkor lesz a az order 'visszaigazolt'
*      az order.confirmInfo a orderitem.confirmInfo-k merge
*   3. ha akár egy is 'notok' akkor az order 'notok'
*      az order.configInfo a cart_item.configInfo-k merge
*   teljesités igazolás folyamata
*   1. a team adminok a orderitem statusban jelzik "success1"
*      és a megrendelő értesitést kap, hogy igazolja vissza a teljesitést
*   2. ha megrendelő visszaigazolhatja a orderitem -ben "success2"
*      EKKOR TÖRTÉNIK A PÉNZ ÁTMOZGATÁS és értesitést kap az éritett team admin is. 
*   3. ha mindegyik tétel success2 akkor az order is "success"  
*/
Route::get('/products/list/{teamId}',[ProductController::class, 'list']);
Route::get('/products/listbyuser/{userId}',[ProductController::class, 'listByUser']);
Route::get('/products/create/{team}',[ProductController::class, 'create'])
	->middleware('auth');
Route::get('/products/{product}/add/{quantity}',[ProductController::class,'add'])
	->middleware('auth');
Route::get('/products/{product}',[ProductController::class, 'show']);
Route::get('/products/{product}/edit',[ProductController::class, 'edit'])
	->middleware('auth');
Route::get('/products/{product}/delete',[ProductController::class, 'delete'])
	->middleware('auth');
Route::get('/products/evaluation/{productId}', [ProductController::class, 'evaluation']);
Route::post('/products/evaluation', [ProductController::class, 'saveevaluation']);
Route::post('/products',[ProductController::class, 'store']);
Route::post('/products/{product}',[ProductController::class, 'update']);
// megjegyzés:product like/dislike csak a teljesitett mgrendelés vevőknek engedélyezett
// product comment a team tagoknak és a teljesitett megrendelés vevőinek engedyélyezett

Route::get('/carts/add',[CartController::class, 'add'])
	->middleware('auth');
// ha van open cart azt folytatja, egyébként újat kezd 
// ?produc_id= &quantity=x
Route::get('/carts/list',[CartController::class, 'show'])
	->middleware('auth');
Route::get('/carts/{itemId}/delete',[CartController::class, 'delete'])
	->middleware('auth');
Route::get('/carts/clear',[CartController::class, 'clear'])
	->middleware('auth');
// show, delete, clear csak a tulajdonosnak engedélyezett
Route::get('/carts/send',[CartController::class, 'send'])
	->middleware('auth');
Route::get('/carts/confirm/{orderItemId}',[CartController::class, 'confirm'])
	->middleware('auth');

Route::get('/orders/list',[OrderController::class, 'list'])
	->middleware('auth');
// ?team, product, user, status
// a megrendelőnek és az érintett team adminoknak elérhető
Route::get('/orders/send/{order}',[OrderController::class, 'send'])
	->middleware('auth');
// csak a cart tulajdonosának engedélyezett
Route::get('/orders/{orderitemId}/confirm',[OrderController::class, 'confirm'])
	->middleware('auth');
// ?status, confirmInfo 
// csak az érintett team adminoknak engedélyezett 
Route::post('/order/doconfirm',[OrderController::class, 'doConfirm']);
Route::get('/orders/cancel/{order}',[OrderController::class, 'cancel'])
	->middleware('auth');
Route::get('/order/listbyproduct/{productId}',[OrderController::class, 'listByProduct']);	
// csak a megrendelőnek engedélyezett status függően
Route::get('/accountInfo',[CurrentaccountController::class, 'info'])
	->middleware('auth');
// csak a tulajdonosnak engedélyezett
Route::get('/evaluation/create/{product}',[EvaluationController::class, 'create'])
	->middleware('auth');
Route::get('/account/list/{actorType}/{actor}',[AccountController::class, 'list'])
	->middleware('auth');
Route::get('/account/send/{accountId}',[AccountController::class, 'send'])
	->middleware('auth');
Route::post('/account/send',[AccountController::class, 'dosend']);
	
// csak a vevőknek engedélyezett
// ---------------------------------------------------------------

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

