<?php
/**
* facebook login
* szükséges config:FB_CLIENT_ID, FB_CLIENT_SECRET
* sikeres login után sessionban lévő 'afterlogin' cimre ugrik
*     default:a MYDOMAIN/opt/users/profile cimre ugrik
*
* a https:developers.facebook.com oldalon kell a klienset regisztrálni
* a "products" részhez adjuk hozzá a "Facebbok login"-t, engedélyezzük,
* ennek beállításait is adjuk meg, "Force Web OAuth Reauthentication" = No legyen
* redirekt uri: MYDOMAIN/opt/fblogin/code
*
*/

function callCurl(string $url, array $post=array(), array $headers=array()):string {
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

class FbloginController extends Controller {

		
	protected function apiRequest(string $url, array $post=array(), array $headers=array()) {
	        $headers[] = 'Accept: application/json';
	        if (isset($post['access_token'])) {
	            $headers[] = 'Authorization: Bearer ' . $post['access_token'];
	        }
	        $response = callCurl($url, $post, $headers);
	        return JSON_decode($response);
	}
	
	protected function readOrCreateUser(string $fbId, string $fbName, string $fbPicture): User {
		$user = new User();
		$w = explode(' ',$fbName);
		$table = new table('users');
		$table->where(['picture','=',$fbPicture]);
		$rec = $table->first();
		if ($rec) {
			// megvan a user rekord
			foreach ($rec as $fn => $fv) {
				$user->$fn = $fv; 			
			}
		} else {
			// még nincs ilyen user rekord
			$user->picture = $fbPicture;
			$user->csaladnev = $fbName;
			$user->nick = $fbName; 
			$user->pswhash = time();
			$user->fbid = $fbId;
			if (count($w) >= 3) {
				$user->csaladnev = $w[0];
				$user->utonev1 = $w[1];
				$user->utonev2 = $w[2];
				$user->nick = $w[1]; 
			}
			if (count($w) == 2) {
				$user->csaladnev = $w[0];
				$user->utonev2 = '';
				$user->utonev1 = $w[0];
				$user->nick = $w[1]; 
			}
			unset($user->csoportok);
			$table->insert($user);
			$user->id = $table->getInsertedId();
		}
		return $user;	
	}
		
	public function authorize(Request &$request) {
	    if (!defined('FB_CLIENT_ID')) {
	        define('FB_CLIENT_ID','00000000');
	        define('FB_CLIENT_SECRET','00000000');
	    }
		$state = time();
		$redirect_uri = config('MYDOMAIN').'/opt/fblogin/code';
		$request->sessionSet('state',$state);
		?>
		<html>
		<body>
		<div style="display:none">
		<form action="https://www.facebook.com/dialog/oauth" method="post" name="form1">
		<input type="text" name="client_id" value="<?php echo FB_CLIENT_ID; ?>000000" />
		<input type="text" name="state" value="<?php echo $state; ?>" />
		<input type="text" name="redirect_uri" value="<?php echo $redirect_uri; ?>" />
		<button type="submit">OK</button>
		</form>
		</div>
		<script type="text/javascript">
		document.forms.form1.submit();
		</script>
		</body>
		</html>
		<?php 
	}
		
	public function code(Request &$request) {
		$code = $request->input('code');
		$state = $request->input('state');
		$afterLogin = $request->sessionGet('afterLogin', config('MYDOMAIN').'/opt/users/profile');
		if ($state != $request->sessionGet('state')) {
			echo 'Fatal error facebbok login. state incorrect'; exit();		
		}
		$redirect_uri = config('MYDOMAIN').'/opt/fblogin/code';
   	    $token = $this->apiRequest(
   	      'https://graph.facebook.com/oauth/access_token', 
   		   ['client_id' => FB_CLIENT_ID,
             'client_secret' => FB_CLIENT_SECRET,
             'redirect_uri' => $redirect_uri,
             'state' => $state,
             'code' => $code
	    		]
	    );
		 if (isset($token->access_token)) {
            $url="https://graph.facebook.com/v2.3/me?'.
            'fields=id,name,picture";
            $request->sessionSet('access_token', $token->access_token);
   			$fbuser = $this->apiRequest(
   				$url, 
					['access_token' => $token->access_token]
				);
	    		if (!isset($fbuser->error)) {
					// $fbuser alapján bejelentkezik (ha még nincs user rekord létrehozza)
					// $fbuser->name, ->id ->picture->data->url 
					$user = $this->readOrCreateUser($fbuser->id, $fbuser->name, $fbuser->picture->data->url);
					$request->sessionSet('loggedUser',$user);					
					redirectTo($afterLogin);
				} else {
					echo 'Fatal error in facebook login. wrong user data '.json_encode($fbuser); return;		 
				}
		 } else {
			echo 'Fatal error in facebook login. access_token not found '.
			'code = '.json_encode($code).
			' response= '.json_encode($token);
			return;		 
		 }	
	}
}
?>