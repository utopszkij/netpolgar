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
        return Socialite::driver('github')->redirect();
    }
    public function loginWithSocial(string $socName, string $field) {
        try {
            $user = Socialite::driver($socName)->user();
            $isUser = User::where($field, $user->id)->first();
            if($isUser){
                Auth::login($isUser);
                return redirect('/dashboard');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    $field => $user->id,
                    'password' => encrypt(date('YmdHis').rand(1000,9999))
                ]);
                Auth::login($createUser);
                return redirect('/dashboard');
            }
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
    public function loginWithFacebook() {
    	return $this->loginWithSocial('facebook','fb_id');
    }
    public function loginWithGoogle() {
    	return $this->loginWithSocial('google','google_id');
    }
    public function loginWithGithub() {
    	return $this->loginWithSocial('github','github_id');
    }
}
