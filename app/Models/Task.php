<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;
    protected $fillable = [
        'name', 'status', 'type', 'project_id', 
        'assign', 'deadline', 'config', 'status'
    ];

    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"project_id":0,
			"deadline":"'.date('Y-m-d').'",
			"status":"proposal",
			"type":"",
			"assign":0,
			"created_at":"'.date('Y-m-d').'",
			"updated_at":"'.date('Y-m-d').'"
    	}');
    	return $result;
    }

	 public static function getInfo($task) {
		$result = JSON_decode('{
			"likeCount":0,
			"disLikeCount":0,
			"userLiked":false,
			"userDisliked":false,
			"commentCount":0		
		}');
		if ($task) {
        // like, disLike, memberCount infok
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','tasks')
        ->where('parent','=',$task->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','tasks')
        ->where('parent','=',$task->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','tasks')
                ->where('parent','=',$task->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','tasks')
                ->where('parent','=',$task->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
        }
		  $result->commentCount = \DB::table('messages')
		  	->where('parent_type','=','tasks')
		  	->where('parent','=',$task->id)
		  	->count();
		}  			
		return $result;	 
	 }

}
