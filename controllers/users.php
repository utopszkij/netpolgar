<?php
/**
 * user kezelés kontroller 
 */
include_once 'controllers/common.php';

/** user kezelés kontroller osztály */
class UsersController extends CommonController {
    
	/**
	 * regisztrációs form kirajzolása
	 * - reg.mód select
	 * - rejtetten mindkét fajta regist form
	 * - Js kód teszi a megfelelőt láthatóvá
	 * @param RequestObject $request
	 */
    public function regist(Request $request) {
        $p = $this->init($request, []);
        $p->title = 'REGIST_TITLE';
        $p->cancelUrl = '';
        $p->avatarUrl = '';
        $p->msgs = [];
        
        $p->id = 0;
        $p->nick = '';
        if (config('smtpHost') != '') {
            $p->enabled = 0; // email aktiválás szükséges
        } else {
            $p->enabled = 1;
        }
        $p->errorcount = 0; 
        $p->block_time = '2099-12-31';
        $p->name = ''; 
        $p->email = '';
        $p->admin = 0;
        $p->avatar = '';
        $p->reg_mode = '';
        $this->createCsrToken($request, $p);
        $this->view->registForm($p);
	}
	
	/**
	 * user rekord tárolása
	 * @param object $data - tárolandó user rekord
	 * @param Request $request - opcionálisan backUrl 
	 * @param UserRecord $user - bejelentkezett user
	 */
	protected function save($data, Request $request, UserRecord  $user ) {
	    $p = $this->init($request, []);
	    $backUrl = urldecode($request->input('backUrl',''));
	    if ($data->enabled == 1) {
	        $data->block_time = '';
	    }
	    $msgs = $this->model->save($data);
	    if (count($msgs) == 0) {
	        if ($data->id == $user->id) {
	            // refresh logged user
	            $user = $this->model->getById($data->id);
	            $request->sessionSet('loggedUser',$user);
	        }
	        if (($data->id == 0) & ($data->reg_mode == 'web')) {
	            $this->model->sendActivateEmail($data->nick);
	            if (config('smtpHost') != '') {
	                $this->view->successMsg(['NEW_USER_SAVED','EMAIL_SENDED'],$backUrl,'OK',$p);
	            } else {
	                $this->view->successMsg(['NEW_USER_SAVED'],$backUrl,'OK',$p);
	            }
	        } else {
	            $this->view->successMsg(['USER_SAVED'],$backUrl,'OK',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,backUrl,txt('OK'),$p);
	    }
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
    	if ($p->id == 0) {
    	    $p->cancelUrl = '';
    	    $p->title = 'REGIST_TITLE';
    	} else {
    	    $p->cancelUrl = '';
    	    $p->title = 'PROFILE';
    	}
    	if ($p->avatar == 'gravatar') {
    	    $p->avatarUrl = 'https://gravatar.com/avatar/'.md5($p->email);
    	} else {
    	    $p->avatarUrl = $p->avatar;
    	}
    	foreach ($data as $fn => $fv) {
    	    $p->$fn = $request->input($fn, $fv);
    	}
    	if ($p->id == 0) {
    	    $p->reg_mode = 'web';
    	}
    	$this->createCsrToken($request, $p);
    	$this->view->registForm($p);
	}
	
	/**
	 * userRecord tárolása
	 * @param Request $request - csrToken, user rekord mezői
	 * sessionban van (vagy nincs) "user" a bejelentkezett user adataival        
	 */
	public function add(Request $request) {
	   $p = $this->init($request, []);
	   $this->checkCsrToken($request);

	   // checkboxok kezelése (ha nincs bejelölve akkor nem érkezik)
	   
	   // record feltöltése a $request-ből
	   $data = new UserRecord();
	   foreach ($data as $fn => $fv) {
	       $data->$fn = $request->input($fn);
	   }
	   
	   // ellenörzés
	   $msgs = $this->model->check($data);
	   if (($data->id > 0) && ($data->psw == '')) {
	       unset($data->pswhash);
	   } else {
	       if ($request->input('psw') != $request->input('psw2')) {
	           $msgs[] = 'PSWS_NOT_EQUALS';
	       }
           if (strlen($request->input('psw')) < 6) {
               $msgs[] = 'PSWS_SORT';
               
           }
           $data->pswhash = hash('sha256',$request->input('psw'));
	   }
	   
	   if (count($msgs) == 0) {
	       // tárolás
	       $this->save($data, $request, $p->loggedUser);
	   } else {
	       // form visszahívása hibaüzenettel
	       $p->msgs = $msgs;
	       $this->recallForm($data,$request,$p);
	   }
	}
	
	/**
	 * userRecord tárolása
	 * @param Request $request - csrToken, user rekord mezői
	 * sessionban van (vagy nincs) "user" a bejelentkezett user adataival
	 */
	public function profilesave(Request $request) {
	    $p = $this->init($request, []);
	    $this->checkCsrToken($request);
	    // checkboxok kezelése (ha nincs bejelölve akkor nem érkezik)
	    
	    // record feltöltése a $request-ből
	    $data = new UserRecord();
	    foreach ($data as $fn => $fv) {
	        $data->$fn = $request->input($fn);
	    }
	    unset($data->nick); // nick nem módosítható
	    
	    // logged user
	    $user = new UserRecord();
	    foreach ($request->sessionGet('loggedUser') as $fn => $fv) {
	        $user->$fn = $request->input($fn);
	    }
	    
	    // ellenörzés
	    $msgs = $this->model->check($data);
	    if (($data->id > 0) && ($request->input('psw') == '')) {
	        unset($data->pswhash);
	    } else {
	        if ($request->input('psw') != $request->input('psw2')) {
	            $msgs[] = 'PSWS_NOT_EQUALS';
	        }
	        if (strlen($request->input('psw')) < 6) {
	            $msgs[] = 'PSWS_SORT';
	            
	        }
	        $data->pswhash = hash('sha256',$request->input('psw'));
	    }
	    
	    if (($user->id != $data->id) &
	        (!$this->model->isAdmin($user->id))) {
	       $msgs[] = 'ACCESS_VIOLATION';        
	    }
	    
	    if (count($msgs) == 0) {
	        // tárolás
	        $this->save($data, $request, $user);
	    } else {
	        // form visszahívása hibaüzenettel
	        $p->msgs = $msgs;
	        $p->backUrl = $request->input('backUrl',MYDOMAIN);
	        $data->nick = $request->input('origNick');
	        $p->userData = new UserRecord();
	        foreach ($data as $fn => $fv) {
	            $p->userData->$fn = $fv;
	        }
	        if ($p->userData->avatar == 'gravatar') {
	            $p->userDataAvatarUrl = 'https://gravatar.com/avatar/'.md5($p->userData->email);
	        } else {
	            $p->userDataAvatarUrl = $p->userData->avatar;
	        }
	        $this->view->profileForm($p);
	    }
	}
	
	public function login(Request $request) {
	    $p = $this->init($request, []);
	    $p->msgs = [];
	    $this->createCsrToken($request, $p);
	    if ($_SERVER['REMOTE_ADDR'] == '192.168.0.12') {
	        // local test
	        redirectTo(config('MYDOMAIN').'/opt/users/accesstoken');
	    } else {
    	    $this->view->loginForm($p);
	    }
	}
	
	/**
	 * email-es aktiválás
	 * @param Request $request code=#####id
	 * - blokkolt IP -ről inditva nem müködik
	 * - ha jó akkor users táblában code törlése, block_time torlése, enabled = true
	 * - hibás kisérlet esetén IP beirása a "hacker" táblában vagy
	 *    errorcount növelése a "hacker" táblában
	 * - jó code setéb IP törlése a "hacker" táblából   
	 */
	public function activate(Request $request) {
	    $p = $this->init($request, []);
	    $this->model->checkIpBlocked();
	    $code = $request->input('code');
	    $id = 0 + substr($code,6,10);
	    $rec = $this->model->getById($id);
	    if (($rec->nick != '') & ($rec->code == $code)) {
	        // sikeres 
	        $this->model->clearIpBlocked();
	        $rec->code = '';
	        $rec->enabled = 1;
	        $rec->block_time = '';
	        $this->model->update($rec);
	        $request->sessionSet('loggedUser', new UserRightsRecord());
	        $this->view->successMsg(['ACCOUNT_ACTIVATED'],'','',$p);
	    } else {
	        $this->view->errorMsg(['FALSE_ACTIVATION'],'','',$p);
	        $this->model->incIpBlocked();
	    }
	}
	
	/**
	 * regisztrációs form feldolgozása
	 * @param Request $request - csrToken, nick, psw
	 * - blokkolt IP -ről inditva nem müködik
	 * - hibás kisérlet esetén IP beirása a "hacker" táblában vagy
	 *    errorcount növelése a "hacker" táblában
	 * - jó code setéb IP törlése a "hacker" táblából   
	 */
	public function dologin(Request $request) {
	    $p = $this->init($request, []);
	    $this->model->checkIpBlocked();
	    $p->msgs = [];
	    $this->checkCsrToken($request);
	    $rec = $this->model->getByNick($request->input('nick'));
	    if (($rec->nick == $request->input('nick','?')) & 
	        ($rec->enabled == 1) &
	        ($rec->pswhash == hash('sha256', $request->input('psw')))) {
	        // sikeres login    
	        $request->sessionSet('loggedUser',$rec);
	        $this->model->clearIpBlocked();
	        redirectTo(MYDOMAIN);
	    } else {
	        $this->model->incIpBlocked();
	        if (($rec->nick == $request->input('nick')) & ($rec->enabled == 1)) {
	            // jelszó hiba
	            $p->msgs = ['FALSE_LOGIN'];
	            $rec->errorcount = $rec->errorcount + 1;
	            if ($rec->errorcount > config('falseLoginLimit')) {
	                $rec->enabled = 0;
	                $rec->block_time = date('Y-m-d H:i:s');
	                $p->msgs[] = 'TOO_MANY_FALSELOGIN';
	            }
	            $this->model->update($rec);
	        } else  if ($rec->enabled == 0) {
                $p->msgs[] = ['ACCOUNT_IS_BLOCKED'];
	        } else {
	            $p->msgs = ['FALSE_LOGIN'];
	        }
	        $this->createCsrToken($request, $p);
	        $this->view->loginForm($p);
	    }
	} // dologin
	
	/**
	 * kijerlentkezés
	 * @param Request $request
	 */
	public function logout(Request $request) {
	    $request->sessionSet('loggedUser', new UserRecord());
	    redirectTo(MYDOMAIN);
	}
	
	/**
	 * forget my nick klikk  email kérdező form kirajzolása
	 * @param Request $request
	 */
	public function forgetnick(Request $request) {
	    $p = $this->init($request, []);
	    $this->createCsrToken($request, $p);
	    $this->view->forgetNick($p);
	}
	
	/**
	 * forget ny psw klikk és forget my nick email form feldolgozás
	 * - generál egy új jelszót 
	 * - és emailben küldi a nick nevet és a jelszót
	 * @param Request $request - csrToken, nick vagy email
	 */
	public function forgetpsw(Request $request) {
	    $p = $this->init($request, []);
	    $this->checkCsrToken($request);
	    $nick = $request->input('nick');
	    $email = $request->input('email');
	    $rec = new UserRecord();
	    $rec->nick = '';
	    $request->sessionSet('user', new UserRecord);
	    if ($nick != '') {
	        $rec = $this->model->getByNick($nick);
	    } else if ($email != '') {
	        $rec = $this->model->getByEmail($email);
	    }
	    if ($rec->nick != '') {
	        $newPsw = rand(100000,999999);
	        $rec->pswhash = hash('sha256',$newPsw);
	        $table = new Table('users');
	        $table->where(['nick','=',$rec->nick]);
	        $table->update($rec);
	        $this->model->sendNickPswEmal($rec->email, $rec->nick, $newPsw);
	        $this->view->successMsg(['EMAIL_SENDED'],'','', $p);
	    } else {
	        $this->view->errorMsg(['NOT_FOUND'],'','', $p);
	    }
	}
	
	/*
	 * aktiváló email újra küldése
	 * @param Request $request - csrToken, nick
	 */
	public function getactivateemail(Request $request) {
	    $p = $this->init($request, []);
	    $request->sessionSet('user', new UserRecord);
	    $this->checkCsrToken($request);
	    $nick = $request->input('nick');
	    if ($nick == '') {
	        $this->view->errorMsg(['NOT_FOUND'], '','', $p);
	    } else {
	       $rec = $this->model->getByNick($nick);
	       if ($rec->nick != '') {
	           $this->model->sendActivateEmail($rec->nick);
	           $this->view->successMsg(['EMAIL_SENDED'], '','', $p);
	       } else {
	           $this->view->errorMsg(['NOT_FOUND'], '','', $p);
	       }
	    }
	}
	
	/**
	 * Bejelentkezett user profil megjelenitése, ha a bejelentkezett user systemadmin akkor
	 * a $requestben userid is érkezhet, ez esetben ennek a profilját jeleniti meg
	 * @param Request $request - opcionálisan: userid, backUrl
	 */
	public function profile(Request $request) {
	    $p = $this->init($request, []);
	    $this->createCsrToken($request, $p);
	    $p->userData = $p->loggedUser;
	    $p->userDataAvatarUrl = $p->avatarUrl;
	    $p->backUrl = urldecode($request->input('backUrl',MYDOMAIN));
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
	        $p->userData = $this->model->getById($p->userId);
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
	 * @request accesstoken, once, state
	 */
	public function accesstoken(Request $request) {
	    $p = $this->init($request, ['token','nonce']);
	    $token = $request->input('token');
	    $url="https://uklogin.tk/userinfo";
	    if ($_SERVER['REMOTE_ADDR'] == '192.168.0.12') {
	        // local test
	        $uklUser = JSON_decode('{"nickname":"admin", "sub":"123456"}');
	    } else {
	        $this->sessionChange($request->input('nonce'), $request);
	        $uklUser = $this->apiRequest($url, ['access_token' => $token] );
        }
	    if (!isset($uklUser->error)) {
	        // $user alapján bejelentkezik (ha még nincs user rekord létrehozza)
	        // $user->nickname, ->audited ->postal_code ->loclity
            $userRec = $this->model->getByNick($uklUser->nickname);
            if ($userRec->id > 0) {
                $request->sessionSet('loggedUser',$userRec);
                redirectTo(config('MYDOMAIN'));
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
                    redirectTo(config('MYDOMAIN'));
                } else {
                    $this->view->errorMsg($msgs,'','',$p);
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
}
?>