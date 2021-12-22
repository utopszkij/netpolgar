<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $model = new \App\Models\Like();
            $exists = $model->where('parent_type','=',$parent_type)
                            ->where('parent','=',$parent)
                            ->where('user_id','=',$user->id)
                            ->where('like_type','=','like')
                            ->first();
            if ($exists) {
                $model->where('parent_type','=',$parent_type)
                ->where('parent','=',$parent)
                ->where('user_id','=',$user->id)
                ->where('like_type','=','like')
                ->delete();
            } else {
                $model->create([
                    "parent_type" => $parent_type,
                    "parent" => $parent, 
                    "user_id" => $user->id, 
                    "like_type" => "like"
                ]);
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
            $model = new \App\Models\Like();
            $exists = $model->where('parent_type','=',$parent_type)
            ->where('parent','=',$parent)
            ->where('user_id','=',$user->id)
            ->where('like_type','=','dislike')
            ->first();
            if ($exists) {
                $model->where('parent_type','=',$parent_type)
                ->where('parent','=',$parent)
                ->where('user_id','=',$user->id)
                ->where('like_type','=','dislike')
                ->delete();
                
            } else {
                $model->create([
                    "parent_type" => $parent_type,
                    "parent" => $parent,
                    "user_id" => $user->id,
                    "like_type" => "dislike"
                ]);
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
        
        $likeUsers = \App\Models\Like::select('users.id', 'users.name', 'users.profile_photo_path', 'users.email')
        ->leftJoin('users','users.id','=','likes.user_id')
        ->where('parent_type', '=', $parentType)
        ->where('parent', '=',$parent)
        ->where('like_type','=','like')
        ->orderBy('name')
        ->get();
        
        $disLikeUsers = \App\Models\Like::select('users.id', 'users.name', 'users.profile_photo_path', 'users.email')
        ->leftJoin('users','users.id','=','likes.user_id')
        ->where('parent_type', '=', $parentType)
        ->where('parent', '=', $parent)
        ->where('like_type','=','dislike')
        ->orderBy('name')
        ->get();
        
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'Ftaal error in likeInfo. parent not found'; exit();
        }

        // $parent -be kell 'name' !
        if ($parentType == 'members') {
            $parent->name = '?';
            $groupTable = \DB::table($parent->parent_type);
            $group = $groupTable->where('id','=',$parent->parent)->first();
            if ($group) {
                $parent->name = $group->name;
            }
            $userTable = \DB::table('users');
            $user = $userTable->where('id','=',$parent->user_id)->first();
            if ($user) {
                $parent->name .= ' / '.$user->name;
            }
        }
        if ($parentType == 'messages') {
            $parent->name = $parent->value;
        }
        return view('like.info',["likeUsers" => $likeUsers,
                                "disLikeUsers" => $disLikeUsers,
                                "parentType" => $parentType,
                                "parent" => $parent]);
    }
    
}
