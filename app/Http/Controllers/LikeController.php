<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikeController extends Controller
{
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
            if ($parent_type == 'teams') {
                $parentModel = new \App\Models\Team();
            }
            if ($parent_type == 'members') {
                $parentModel = new \App\Models\Member();
            }
            if (method_exists($parentModel, 'checkStatus')) {
                $parentModel->checkStatus($parent);
            }
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
            if ($parent_type == 'teams') {
                $parentModel = new \App\Models\Team();
            }
            if ($parent_type == 'members') {
                $parentModel = new \App\Models\Member();
            }
            if (method_exists($parentModel, 'checkStatus')) {
                $parentModel->checkStatus($parent);
            }
        }
        return \Redirect::back();
    }
    
}
