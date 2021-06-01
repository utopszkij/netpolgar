<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Validator;
use Socialite;
use Exception;
use Auth;

class SocialController extends Controller {
    public function facebookRedirect() {
        return Socialite::driver('facebook')->redirect();
    }
    public function googleRedirect() {
        return Socialite::driver('google')->redirect();
    }
    public function githubRedirect() {
		$url = 'https://github.com/login/oauth/authorize'.
		  '?client_id='.env('Github_app_id').
		  '&scope=user'.
		  '&state=123456789'.
		  '&login=LoginStr';
		  echo '<script>location="'.$url.'";</script>'; exit();
//        return Socialite::driver('github')->redirect();
    }
    
	/**
     * távoli URL hívás   FT.2021.05.30.
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
	 * távol API hívás     FT.2021.05.30.
	 * @param string $url
	 * @param array $post
	 * @param array $headers
	 * @return mixed
	 */
	protected function apiRequest(string $url, array $post=array(), array $headers=array()) {
	        $headers[] = 'Accept: application/json';
	        if (isset($post['access_token'])) {
	            $headers[] = 'Authorization: Bearer ' . $post['access_token'];
               $post = [];
	        }
	        $response = $this->callCurl($url, $post, $headers);
	        return JSON_decode($response);
	}    
    
	protected function makeUnique(string $field, string $value):string {
	    $i = 1;
	    $result = $value;
	    $w = \DB::table('users')->where($field,'=',$result)->first();
	    while ($w) {
	        $result = $value.'-'.$i;
	        $i++;
	        $w = \DB::table('users')->where($field,'=',$result)->first();
	    }
	    return $result;
	}
	
    public function loginWithSocial(string $socName, string $field) {
        try {
            $user = Socialite::driver($socName)->user();
            $isUser = User::where($field, $user->id)->first();
            if($isUser){
                Auth::login($isUser);
                return redirect('/');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    $filed => $user->id,
                    'password' => encrypt(date('YmdHis').rand(1000,9999))
                ]);
                Auth::login($createUser);
                return redirect('/');
            }
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
    
    public function loginWithFacebook() {
    	if (\Request::input('code','') != '') {
			$code = \Request::input('code');
	   	$state = \Request::input('state');
      	$token = $this->apiRequest(
   	      'https://graph.facebook.com/oauth/access_token',
   		   ['client_id' => env('Facebook_app_id'),
             'client_secret' => env('Facebook_secret'),
   		     'grant_type' => 'authorization_code',
             'redirect_uri' => \URL::to('/').'/auth/facebook/callback',
             'state' => $state,
             'code' => $code
   		   ]
	    	);
	    	if (isset($token->access_token)) {
            $url="https://graph.facebook.com/v2.3/me?fields=id,name,picture";
   			$fbuser = $this->apiRequest(
   				$url,
					['access_token' => $token->access_token]
				);
			   if (!isset($fbuser->error)) {
			    		// sikeres fb login guser:{id, name, picture, email}
						try {
							// nézzük van-e már ilyen user rekord?
				            $isUser = User::where('fb_id', $fbuser->id)->first();
								if (!$isUser) {
				            	 $isUser = User::where('name', $fbuser->name)->first();
							}

							// ha van bejelentkeztetjük, ha nincs létrehozzuk												            
				            if($isUser){
				                Auth::login($isUser);
				            }else{
					            $createUser = User::create([
					                    'name' => $this->makeUnique('name',$fbuser->name),
					                    'email' => 'none_'.$fbuser->id.'@fb.fb',
					                    'fb_id' => $fbuser->id,
					                    'email_verified_at' => date('Y-m-d'),
					                    'password' => encrypt(date('YmdHis').rand(1000,9999))
					            ]);
					            Auth::login($createUser);
				           } 	 	
				        } catch (Exception $exception) {
				            dd($exception->getMessage());
				        }			    		
			   } else {
			    		echo 'Fatal error facebook login '.JSON_encode($fbuser->error); exit();
			   }	
			} else {
		    		echo 'Fatal error facebook login invalid call'; exit();
			}
			return redirect('/');	
	    }
	    exit();	
    	// return $this->loginWithSocial('facebook','fb_id');
    }
    public function loginWithGoogle() {
		/**
		* valamiért az eredeti nem mükszik. amikor ide jön a vezérlés akkor:
		*  $request->input -ban van sate, code, scope, authuser=0, promtt=consent
    	*/
    	if (\Request::input('code','') != '') {
			$code = \Request::input('code');
	   	$state = \Request::input('state');
      	$token = $this->apiRequest(
   	      'https://oauth2.googleapis.com/token',
   		   ['client_id' => env('Google_app_id'),
             'client_secret' => env('Google_secret'),
   		     'grant_type' => 'authorization_code',
             'redirect_uri' => \URL::to('/').'/auth/google/callback',
             'state' => $state,
             'code' => $code
   		   ]
	    	);
		   if (isset($token->access_token)) {
		   		$url="https://www.googleapis.com/oauth2/v1/userinfo?alt=json";
	   			$guser = $this->apiRequest(
	   				$url,
					   ['access_token' => $token->access_token]);
			    	if (!isset($guser->error)) {
			    		// sikeres google login guser:{id, name, picture, email}
						try {
							// nézzük van-e már ilyen user rekord?
						    $isUser = User::where('google_id', $guser->id)->first();
						    if (!$isUser) {
				            	 $isUser = User::where('email', $guser->email)->first();
				            	 Auth::login($isUser);
						    }else{
							    $isUser = User::where('email', $guser->email)->first();
							    if ($isUser) {
							        Auth::login($isUser);
							    }else{
					                $createUser = User::create([
					                    'name' => $this->makeUnique('name',$guser->name),
					                    'email' => $guser->email,
					                    'google_id' => $guser->id,
					                    'email_verified_at' => date('Y-m-d'),
					                    'password' => encrypt(date('YmdHis').rand(1000,9999))
					                ]);
					                Auth::login($createUser);
							    }
				           } 	 	
				        } catch (Exception $exception) {
				            dd($exception->getMessage());
				        }			    		
			    	} else {
			    		echo 'Fatal error google login '.JSON_encode($guser->error); exit();
			    	}	
		   } else {
	    		echo 'Fatal error google login not access_token '.JSON_encode($token); exit();
		   } 
	   } else {
    		echo 'Fatal error github login incorrect call'; exit();
	   }
      return redirect('/');
    }
    
    public function loginWithGithub() {
        
        // sajnos ez nem müködik a guser null értéket ad vissza :(
        
    	if (\Request::input('code','') != '') {
			$code = \Request::input('code');
	   	$state = \Request::input('state');
      	$token = $this->apiRequest(
   	      'https://github.com/login/oauth/access_token',
   		   ['client_id' => env('Github_app_id'),
             'client_secret' => env('Github_secret'),
             'redirect_uri' => \URL::to('/').'/auth/github/callback',
             'state' => $state,
             'code' => $code
   		   ]
	    	);
		   if (isset($token->access_token)) {
		   		echo 'access_token='.JSON_encode($token).'<br>';
		   		$url="https://api.github.com/user";
	   			$guser = $this->apiRequest($url,['access_token' => $token->access_token]);
	   			echo JSON_encode($guser); exit();	
	   	} else {
	    		echo 'Fatal error github login not access_token'; exit();
	   	}			
		} else {
    		echo 'Fatal error github login incorrect call'; exit();
		}    	
		return redirect('/') ;   	
    	// return $this->loginWithSocial('github','github_id');
    }
}
