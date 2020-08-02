<?php
/**
 * user kezelés kontroller
 */
include_once 'controllers/common.php';

/** user kezelés kontroller osztály */
class UsersController extends CommonController {

    function __construct() {
        $this->cName = 'users';
    }

	/**
	 * form visszahívása hibaüzenettel
	 * @param object $data
	 * @param Request $request
	 * @param Params $p
	 */
	protected function recallForm($data,Request $request, Params $p) {
    	foreach ($data as $fn => $fv) {
    	    $p->$fn = $fv;
    	}
   	    $p->title = 'PROFILE';
    	if ($p->avatar == 'gravatar') {
    	    $p->avatarUrl = 'https://gravatar.com/avatar/'.md5($p->email);
    	} else {
    	    $p->avatarUrl = $p->avatar;
    	}
    	foreach ($data as $fn => $fv) {
    	    $p->$fn = $request->input($fn, $fv);
    	}
    	$this->createCsrToken($request, $p);
    	$this->view->profileForm($p);
	}

	/**
	 * userRecord tárolása
	 * @param Request $request - csrToken, user rekord mezői
	 * sessionban van (vagy nincs) "user" a bejelentkezett user adataival
	 */
	public function profilesave(Request $request) {
	   $p = $this->init($request, []);
	   $this->checkCsrToken($request);

	   // ha szükséges ide jön a checkboxok kezelése (ha nincs bejelölve akkor a html nem küldi)

	   // record feltöltése a $request-ből
	   $data = $this->model->getById($request->input('id'));
	   foreach ($data as $fn => $fv) {
	       if ($request->input($fn,'~') != '~')
	       $data->$fn = $request->input($fn);
	   }

	   // ellenörzés
	   $msgs = $this->model->check($data);
	   if ($data->id != $p->loggedUser->id) {
	       $msgs[] = txt('ACCESS_VIOLATION');
	   }
	   if (count($msgs) == 0) {
	       // tárolás
	       $request->sessionSet('loggedUser',$this->model->getById($data->id));
	       $this->model->save($data);
	   } else {
	       // form visszahívása hibaüzenettel
	       $p->msgs = $msgs;
	       $this->recallForm($data,$request,$p);
	   }
	   $this->redirectTo(config('MYDOMAIN'));
	}

	/**
	 * user login 
	 * @param Request $request - uklogin=1 - test config ellenére uklogin
	 */
	public function login(Request $request) {
	    $p = $this->init($request, ['uklogin']);
	    if ($p->uklogin != 1) {
    	    // local test
    	    if ((substr($_SERVER['REMOTE_ADDR'],0,7) == '192.168') | 
    	        (config('TESTVERSION') == 1)) {
    	        $this->view->testLogin($p);
    	        return;
    	    }
	    }
	    $ukloginUrl = 'https://uklogin.tk/openid/authorize/'.
	   	    '?client_id='.urlencode(config('MYDOMAIN').'/opt/users/accesstoken/').
	   	    '&nonce='.session_id().
	   	    '&redirect_uri='.urlencode(config('MYDOMAIN').'/opt/users/accesstoken/').
	   	    '&policy='.urlencode(config('MYDOMAIN').'opt/policy/show').
	   	    '&scope='.urlencode('sub nickname audited');
	    $this->redirectTo($ukloginUrl);
	}
	
	/**
	 * test login, ha nincs meg az adatbázisban akkor létrehozza
	 * @param Request $request user="testuser" vagy 'testadmin'
	 */
	public function testlogin(Request $request) {
	    $p = $this->init($request, ['user']);
	    global $REQUEST;
	    $user = new UserRecord();
	    $table = new table('users');
	    if ($p->user == 'testadmin') {
	        $user->id = 3;
	        $user->nick = $p->user;
	        $user->admin = 1;
	        $user->enabled = 1;
	        $user->avatar = "https://image.flaticon.com/icons/svg/2856/2856679.svg";
	        $REQUEST->sessionSet('loggedUser', $user);
	        $w = $this->model->getById($user->id);
	        if ($w->id <= 0) {
	            $table->insert($user);
	        }
	        $this->redirectTo(config('MYDOMAIN'));
	    } else if ($p->user == 'testuser') {
	        $user->id = 4;
	        $user->nick = $p->user;
	        $user->admin = 0;
	        $user->avatar = "https://image.flaticon.com/icons/svg/1876/1876698.svg";
	        $user->enabled = 1;
	        $REQUEST->sessionSet('loggedUser', $user);
	        $w = $this->model->getById($user->id);
	        if ($w->id <= 0) {
	            $table->insert($user);
	        }
	        $this->redirectTo(config('MYDOMAIN'));
	    }
	    return;
	}

	/**
	 * kijerlentkezés
	 * @param Request $request
	 */
	public function logout(Request $request) {
	    $request->sessionSet('loggedUser', new UserRecord());
	    $this->redirectTo(MYDOMAIN);
	}

	/**
	 * Bejelentkezett user profil megjelenitése, ha a bejelentkezett user systemadmin akkor
	 * a $requestben userid is érkezhet, ez esetben ennek a profilját jeleniti meg
	 * @param Request $request - opcionálisan: userid, backUrl
	 */
	public function profile(Request $request) {
	    $p = $this->init($request, []);
	    $this->createCsrToken($request, $p);
	    $p->loggedUser = $this->model->getById($p->loggedUser->id);
	    $p->userData = $p->loggedUser;
	    $p->userDataAvatarUrl = $p->avatarUrl;
	    $p->backUrl = urldecode($request->input('backUrl',MYDOMAIN));
	    $p->formTitle = $p->loggedUser->nick.' '.txt('PROFILE');
	    if ($p->userAdmin) {
	        $w = $request->input('userid');
	        if ($w != '') {
	            $p->userData = $this->model->getById($w);
	            if ($p->userData->avatar == 'gravatar') {
	                $p->userDataAvatarUrl = 'https://gravatar.com/avatar/'.md5($p->userData->email);
	            } else {
	                $p->userDataAvatarUrl = $p->userData->avatar;
	            }
	        }
	    }
	    if ($p->userData->id > 0) {
	        $this->view->profileForm($p);
	    } else {
	        $this->view->errorMsg(['NOT_FOUND'], '','', $p);
	    }
	}

	/**
	 * fiók törlése biztonsági kérdés megjelenítése
	 * @param Request $request - optional: userid, backUrl
	 */
	public function removeaccount(Request $request) {
	    $p = $this->init($request, []);
	    $p->userId = 0 + $request->input('userid', $p->loggedUser->id);
	    $p->backUrl = $request->input('backUrl',MYDOMAIN);
	    if ((!$p->userAdmin) & ($p->loggedUser->id != $p->userId)) {
	        $this->view->errorMsg(['ACCESS_VOILOATION'],$p->backUrl,txt('OK'),$p);
	    }
	    if ($p->userId > 1) {
	        $this->createCsrToken($request, $p);
	        $p->user = $this->model->getById($p->userId);
	        $this->view->removeaccount($p);
	    } else {
	        $this->view->errorMsg(['NOT_FOUND'],$p->backUrl,txt('OK'),$p);
	    }
	}

	/**
	 * fiók törlése végrehajtása
	 * @param Request $request {opcionálisan userId, backUrl}
	 * - sessionban a bejelentkezett user
	 */
	public function doremoveaccount(Request $request) {
	    $p = $this->init($request, []);
	    $p->userId = $request->input('userId', $p->loggedUser->id);
	    $p->backUrl = $request->input('backUrl', MYDOMAIN);
	    $this->checkCsrToken($request);
	    if ($p->userId > 1) {
	        $this->model->remove($p->userId);
	        $this->view->successMsg(['ACCOUNT_REMOVED'],$p->backUrl,txt('OK'),$p);
	    } else {
	        $this->view->errorMsg(['NOT_FOUND'],$p->backUrl,txt('OK'),$p);
	    }
	}

	/**
	 * távoli URL hívás
	 * @param string $url
	 * @param array $post ["név" => "érték", ...]
	 * @param array $headers
	 * @return string
	 */
	protected function callCurl(string $url, array $post=array(), array $headers=array()):string {
	    $return = '';
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    if(count($post)>0) {
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	    }
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $return = curl_exec($ch);
	    return $return;
	}

	/**
	 * uklogin callback function
	 * @request accesstoken, once, state, nick
	 *   nick csak localis testnél érkezik
	 */
	public function accesstoken(Request $request) {
        $request->set('option','users');
	    $p = $this->init($request, ['token','nonce','ninck']);
	    $p->nick = $request->input('nick','admin');
	    $token = $request->input('token');
	    $url="https://uklogin.tk/openid/userinfo";
	    if ($_SERVER['REMOTE_ADDR'] == '192.168.0.12') {
	        // local test
	        $uklUser = JSON_decode('{"nickname":"'.$p->nick.'", "sub":"123456"}');
	    } else {
	        $this->sessionChange($request->input('nonce'), $request);
	        $uklUser = JSON_decode($this->callCurl($url, ['access_token' => $token] ));
        }
	    if (!isset($uklUser->error)) {
	        // $user alapján bejelentkezik (ha még nincs user rekord létrehozza)
	        // $user->nickname, ->audited ->postal_code ->loclity
            $userRec = $this->model->getByNick($uklUser->nickname);
            if ($userRec->id > 0) {
                $request->sessionSet('loggedUser',$userRec);
                $this->redirectTo(config('MYDOMAIN'));
            } else {
                $userRec = new UserRecord();
                $userRec->nick = $uklUser->nickname;
                if (substr($uklUser->sub,0,2) == 'f_') {
                    $userRec->reg_mode = 'facebook';
                } else if (substr($uklUser->sub,0,2) == 'g_') {
                    $userRec->reg_mode = 'google';
                } else {
                    $userRec->reg_mode = 'uklogin';
                }
                $userRec->email = 'none';
                $userRec->name = $uklUser->nickname;
                $userRec->avatar = 'gravatar';
                $userRec->id = 0;
                $msgs = $this->model->save($userRec);
                if (count($msgs) == 0) {
                    $request->sessionSet('loggedUser',$userRec);
                    $this->redirectTo(config('MYDOMAIN'));
                } else {
        	        echo 'Fatal error in uklogin. wrong user data '.JSON_encode($msgs); return;
                }
            }
	    } else {
	        echo 'Fatal error in uklogin. wrong user data '.json_encode($uklUser); return;
	    }
	}

	/*
	 * ========================================================================
	 * BROWSER
	 * =======================================================================
	 */

	/**
	 * user böngésző csak admin használhatja
	 * @param Request $request {userAdmin}
	 * -sessionba jöhet: offset, orderField, orderDir, filterStr, limit
	 */
	public function list(Request $request) {
	    $p = $this->init($request, []);
	    if ($p->userAdmin) {
	        $p->offset = $request->input('offset', $request->sessionGet('usersOffset',0));
	        $p->limit = $request->input('limit', $request->sessionGet('usersLimit',20));
	        $p->filterStr = $request->input('filterStr', $request->sessionGet('usersFilterStr',''));
	        $p->orderField = $request->input('orderField', $request->sessionGet('usersOrderField','nick'));
	        $p->orderDir = $request->input('orderDir', $request->sessionGet('usersOrderDir','ASC'));
	        $request->sessionSet('usersOffset',$p->offset);
	        $request->sessionSet('usersLimit',$p->limit);
	        $request->sessionSet('usersOrderField',$p->orderField);
	        $request->sessionSet('usersOrderDir',$p->orderDir);
	        $request->sessionSet('usersFilterStr',$p->filterStr);
	        $p->total = 0;
	        $p->items = $this->model->getRecords($p, $p->total);

	        // váltakozó trClass beállítás /bootstrap table-stiped nem müködik :( /
	        $trClass = 'tr0';
	        foreach ($p->items as $item) {
	            $item->trClass = $trClass;
	            if ($trClass == 'tr0') {
	                $trClass = 'tr1';
	            } else {
	                $trClass = 'tr0';
	            }
	        }

	        $this->createCsrToken($request, $p);
	        $this->view->browser($p);
	    } else {
	        $this->view->errorMsg(['ACCESS_VIOLATION'],'','',$p);
	    }
	}
	
	/**
	 * publikus user profil
	 * @param Request $request - user_id
	 */
	public function form(Request $request) {
	    $p = $this->init($request,['id']);
	    $this->view->setTemplates($p,['navbar','footer']);
	    $this->createCsrToken($request, $p);
	    $p->loggedUser = $this->model->getById($p->loggedUser->id);
	    $p->userData = $this->model->getById($p->id);
	    $p->userDataAvatarUrl = $p->avatarUrl;
	    $p->backUrl = MYDOMAIN;
	    $p->formTitle = $p->userData->nick; 
	    if ($p->userData->id > 0) {
	        $this->view->profileForm($p);
	    } else {
	        $this->view->errorMsg(['NOT_FOUND'], '','', $p);
	    }
	}
}
?>
