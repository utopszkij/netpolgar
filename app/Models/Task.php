<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;


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

	/**
	 * projekt tagok lekérdezése
	 * @param int $projectId
	 * @return array
	 */ 
    public static function getMembers(int $projectId) {		
			return \DB::table('members')
				->select('members.user_id', 'users.name', 'users.profile_photo_path')
				->leftJoin('users','users.id','members.user_id')
				->where('members.parent_type','=','projects')
				->where('members.parent','=',$projectId)
				->where('members.status','=','active')
				->distinct()
				->get();
	}
		 /**
	 * project rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 public static function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			
			$taskArr = [];
			$taskArr['project_id'] = $request->input('project_id');
			$taskArr['name'] = strip_tags($request->input('name'));
			$taskArr['deadline'] = $request->input('deadline');
			$taskArr['type'] = $request->input('type');
			$taskArr['status'] = $request->input('status');
			$taskArr['assign'] = $request->input('assign');
			if ($id == 0) {
				if (\Auth::user()) {
					$taskArr['created_by'] = \Auth::user()->id;
				} else {
					$taskArr['created_by'] = 0;
				}		
			}
			// task rekord tárolás az adatbázisba
			try {
				if ($id == 0) {
			 		$taskRec = Task::create($taskArr);
			 		$id = $taskRec->id;
			 	} else {
					Task::where('id','=',$id)->update($taskArr);			 	
			 	}	
				$errorInfo = '';
			} catch (\Illuminate\Database\QueryException $exception) {
		      $errorInfo = JSON_encode($exception->getMessage());
			}	
			return $errorInfo;		
	 }	

	/* Request jó rekordot tartalmaz?
	 * @param Request $request
	 * @return bool
	 */ 
 	public static function valid(Request $request):bool {
		if (!defined('UNITTEST')) { 
			$request->validate([
				'name' => 'required',
				'type' => 'required',
				'status' => 'required',
				'deadline' => 'required'
			]);
		}	
		// ide csak akkor kerül a vezérélés ha minden OK, egyébként redirekt az elöző oldalra
		return true;
	}		



}
