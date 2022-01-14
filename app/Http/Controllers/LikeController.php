<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller {
    /**
     * szükség esetén a parent rekord status modositása a like /dislike szám szarint
     * @param string $parentType
     * @param unknown $parent
     */
    protected function checkStatus(string $parentType, $parent) {
    	$parentModel = false;
        if ($parentType == 'teams') {
            $parentModel = new \App\Models\Team();
        }
        if ($parentType == 'members') {
            $parentModel = new \App\Models\Member();
        }
        if ($parentType == 'messages') {
            $parentModel = new \App\Models\Message();
        }
        if ($parentType == 'polls') {
            $parentModel = new \App\Models\Poll();
        }
        if ($parentType == 'options') {
            $parentModel = new \App\Models\Option();
        }
        if ($parentType == 'projects') {
            $parentModel = new \App\Models\Project();
        }
        if ($parentModel) {
        	if (method_exists($parentModel, 'checkStatus')) {
            $parentModel->checkStatus($parent);
        	}
        }
    }
    
     /**
     * a bejelentkezett felhasználó a like ikonra kattintott
     * - ha korábban már lájkolta akkor törli azt a likes táblából
     * - ha korábban még nem lájkolta a akkor létrehozza a likes táblában
     * @param string $parent_type
     * @param string $parent
     * @return laravel redirect back
     */
    public function like(string $parent_type, string $parent) {
        $user = \Auth::user();
        if ($user) {
            $exists = Like::getRecord($parent_type, $parent, $user->id, 'like');
            if ($exists) {
				 Like::delRecord($parent_type, $parent, $user->id, 'like');
            } else {
				 Like::createRecord($parent_type, $parent, $user->id, 'like');
            }
            $this->checkStatus($parent_type, $parent);
        }
        return \Redirect::back();
    }
        
    /**
     * a bejelentkezett felhasználó a disLike ikonra kattintott
     * - ha korábban már disLájkolta akkor törli azt a likes táblából
     * - ha korábban még nem dislájkolta a akkor létrehozza a likes táblában
     * @param string $parent_type
     * @param string $parent
     * @return laravel redirect back
     */
    public function disLike(string $parent_type, string $parent) {
        $user = \Auth::user();
        if ($user) {
            $exists = Like::getRecord($parent_type, $parent, $user->id, 'dislike');
            if ($exists) {
				 Like::delRecord($parent_type, $parent, $user->id, 'dislike');
            } else {
				 Like::createRecord($parent_type, $parent, $user->id, 'dislike');
            }
            $this->checkStatus($parent_type, $parent);
        }
        return \Redirect::back();
    }
    
    /**
     * like/dislike user lisita megjelenítése
     * @param string $parentType
     * @param string $parent
     * @return laravel view
     */
    public function likeInfo(string $parentType, string $parent) {
        $likeUsers = Like::getList($parentType, $parent, 'like');
        $disLikeUsers = Like::getList($parentType, $parent, 'dislike');
        
		$parent = Like::getParent($parentType, $parent);
        return view('like.info',["likeUsers" => $likeUsers,
                                "disLikeUsers" => $disLikeUsers,
                                "parentType" => $parentType,
                                "parent" => $parent]);
    }
    
}
