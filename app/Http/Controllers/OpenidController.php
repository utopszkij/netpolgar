<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class OpenidController extends Controller {     
    
    
    protected $model = false;
    
    function __construct() {
    }
    
    /**
     * authorization OpenId végpont
     * most nem biztos, hogy be van jelentkezve a user
     */ 
    public function authorization(Request $request) {
		$redirect_uri =  $request->input('redirect_uri','');
		$state =  $request->input('state','');
		$scope =  $request->input('scope','');
		$policy_uri =  $request->input('policy_uri','');
		
		session()->put('openid_redirect_uri',$redirect_uri);
		session()->put('openid_scope',$scope);
		session()->put('openid_state',$state);
		session()->put('openid_policy_uri',$policy_uri);
		$user = \Auth::user();
		if ($user) {
			if ($user) {
				$token = base64_encode($user->id.$user->name.$user->email);
				$token = str_replace('=','',$token);
				
				return redirect(urldecode($redirect_uri).
				'?token='.$token.
				'&state='.$state);
			}
		} else {
			return redirect('/authorization2');
		}
	}

	/**
	 * openId segéd végpont. Most buztos, hogy be van jelentkezve a user
	 * és a sessionban bent vannak az OpenId paraméterek
	 */ 
	public function authorization2() {
		$redirect_uri =  session()->get('openid_redirect_uri','');
		$state =  session()->get('openid_state','');
		$scope =  session()->get('openid_scope','');
		$user = \Auth::user();
		if ($user) {
			$token = base64_encode($user->id.$user->name.$user->email);
			$token = str_replace('=','',$token);
			session()->put('openid_redirect_uri','');
			session()->put('openid_redirect_uri','');
			session()->put('openid_policy_uri','');
			
			return redirect(urldecode($redirect_uri).
			'?token='.$token.
			'&state='.$state);
		}
	}
	
	/**
	 * Openid userinfo végpont
	 * user be van jelentkezve, sessionban van openid_scope adat
	 */ 
	public function userinfo(Request $request) {
		// result loged user info json string
		$user = \Auth::user();
		$access_token = '='.$request->input('access_token').'=';
		$scope = session()->get('openid_scope','');	
		session()->set('openid_scope','');	
		$token = base64_encode($user->id.$user->name.$user->email);
		if (($user) && ($token === $access_token)) {
			$result = new \stdClass();
			if (strpos(' '.$scope,'id') > 0) {
				$result->id = $user->id;
			}	
			if (strpos(' '.$scope,'name') > 0) {
				$result->name = $user->name;
			}	
			if (strpos(' '.$scope,'email') > 0) {
				$result->email = $user->email;
			}	
			if (strpos(' '.$scope,'email_verified') > 0) {
				$result->email_verified = ($user->email_verified);
			}	
			if (strpos(' '.$scope,'picture') > 0) {
				$result->picture = stripcslashes(urldecode($user->profile_photo_url));
			}	
			return response()->json($result);
		} else {
			echo '{"id":0, "name":"guest"}';
		}
	}
 
}
