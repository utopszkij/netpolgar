<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'created_by', 'description', 'parent', 
        'avatar', 'config', 'status'
    ];
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"parent":0,
			"description":"",
			"avatar":"/img/team.png",
			"status":"proposal",
			"config":{
				"ranks":["admin","president","manager","moderator"],
				"close":98,
				"memberActivate":2,
				"memberExclude":95,
				"rankActivate":40,
				"rankClose":95,
				"projectActivate":2,
				"productActivate":50,
				"subTeamActivate":2,
				"debateActivate":2
			},
			"activated_at":"1900-01-01",
			"created_at":"1900-01-01",
			"closed_at":"1900-01-01",
			"updated_at":"1900-01-01",
			"created_by":0
    	}');
    	return $result;
    }
    
    public static function getInfo(int $id) {
    	// parentClosed ha bármelyik path elem closed
    	// path: [{id,name},...} 
    	// userRank [ 'active_member', 'proposal_admin', ...]
		$result = JSON_decode('{
			"status":"active",
			"path":[],
			"parentClosed":false,        
			"userRank":[],
			"userParentRank":[],
			"userLiked":false,
			"userDisLiked":false,
			"likeCount":0,
			"likeReq":0,
			"disLikeCount":0,
			"disLikeReq":0
		}');
		if ($id == 0) {
			$result->status = 'active';
			if (\Auth::user()) {
				$result->userRank = ['active_member'];
				$result->userParentRank = ['active_member'];
			}	
			return $result;
		}

		$t = \DB::Table('teams');
		$team = $t->where('id','=',$id)->first();
		
		if (!$team) {
			return $result;
		}
		$result->status = $team->status;
		
		$result->path = [];
		while (is_object($team))  {
			if ($team->status == 'closed') {
					$result->parentClosed = true;			
			}			
			$result->path = array_merge([new \stdClass()], $result->path);
			$result->path[0]->id = $team->id;
			$result->path[0]->name = $team->name;
			$t = \DB::Table('teams');
			$team = $t->where('id','=',$team->parent)->first();
		}		

		if (\Auth::user()) {
			$t = \DB::Table('members');
			$items = $t->where('parent','=',$id)
						 ->where('parent_type','=','teams')
						 ->where('user_id','=',\Auth::user()->id)
						 ->get();
			foreach ($items as $item) {
				$result->userRank[] = $item->status.'_'.$item->rank;			
			}
							 
			if (count($result->path) > 0) {
				$t = \DB::Table('members');
				$items = $t->where('parent','=',$result->path[0]->id)
							 ->where('parent_type','=','teams')
							 ->where('user_id','=',\Auth::user()->id)
							 ->where('status','=','active')
							 ->get();
				foreach ($items as $item) {
					$result->userParentRank[] = $item->rank;			
				}
			} else {
				$result->userParentRank = ['member'];			
			}
		}
			
		return $result;
    }
}


