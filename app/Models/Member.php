<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'user_id', 'rank', 'status', 'created_by'
    ];
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"parent_type":"",
			"parent":0,
			"rank":"",
			"status":"/img/team.png",
			"user_id":0,
			"activated_at":"1900-01-01",
			"created_at":"1900-01-01",
			"closed_at":"1900-01-01",
			"updated_at":"1900-01-01",
			"created_by":0
    	}');
    	return $result;
    }
    
    /**
     * 
     * @param string $parent_type
     * @param object $parent
     * @param array $data [{user_id, rank, status},...]
     * @return object
     */
    public static function getInfo(string $parent_type, $parent, $data) {
      $result = JSON_decode('{
        		"ranks":[],
        		"userRank":[],
				"likeCount":0,
				"likeReqMember":0,
				"likeReqRank":0,
				"disLikeCount":0,
				"disLikeReqMember":0,        
				"disLikeReqRank":0,
				"userLiked":[],
				"userDisLiked":[]        
      }');
      $userRank = [];
		if (\Auth::user()) {
		  		$user = \Auth::user();
		  		foreach ($data as $m) {
					if (($m->user_id == $user->id) & ($m->status == 'active')) {
						$userRank[] = $m->rank; 					
					}		  		
		  		}
		}	        			 
		$result->userRank = $userRank;	
      $ranks = [];	
      if (isset($parent->config)) {
				$config = JSON_decode($parent->config);
				if (is_string($config->ranks)) {
					$config->ranks = explode(',',$config->ranks);				
				}        
            if (isset($config->ranks)) {
					$ranks = $config->ranks;         
        		}
      }	
		$result->ranks = $ranks;
		return $result;
    }
    
    /**
     * like/dilike ellenörzés, ha szülséges status modosítás
     * @param string $teamId
     * @return void
     */
    public function checkStatus(string $memberId):void {
        $model = new \App\Models\Member();
        $member = $model->where('id','=',$memberId)->first();
        if ($member) {
            
            $m = \DB::table('likes');
            $likeCount = $m->where('parent_type','=','members')
            ->where('parent','=',$member->id)
            ->where('like_type','=','like')
            ->count();
            
            $m = \DB::table('likes');
            $disLikeCount = $m->where('parent_type','=','members')
            ->where('parent','=',$member->id)
            ->where('like_type','=','like')
            ->count();
            
            $m = \DB::table('members');
            $memberCount = $m->selectRaw('distinct user_id')
                            ->where('parent_type','=',$member->parent_type)
                            ->where('parent','=',$member->parent)
                            ->where('rank','=','member')
                            ->where('status','=','active')
                            ->count(); 
            
            // $config beolvasása {memberActivate, memberExclude, rankActivate, rankClose}
            if ($member->parent_type == 'teams') {
                $parentModel = new \App\Models\Team();
            }
            $parent = $parentModel->where('id','=',$member->parent)->first();
            if (!$parent) {
                echo 'Fatal error parent not found'; exit();
            }
            $config = JSON_decode($parent->config);
            
            if (($member->status == 'proposal') &
                ($member->rank == 'member') &
                ($likeCount >= $config->memberActivate)) {
                    $model->where('id','=',$member->id)->update(['status' => 'active']);
            }
            if (($member->status == 'proposal') &
                ($member->rank != 'member') &
                ($likeCount >= round($config->rankActivate * $memberCount / 100))) {
                        $model->where('id','=',$member->id)->update(['status' => 'active']);
            }
            if (($member->status == 'activel') &
                ($member->rank == 'member') &
                ($disLikeCount >= round($config->memberExclude * $memberCount / 100))) {
                    $model->where('id','=',$member->id)->update(['status' => 'excluded']);
            }
            if (($member->status == 'activel') &
                ($member->rank != 'member') &
                ($disLikeCount >= round($config->rankClose * $memberCount / 100))) {
                    $model->where('id','=',$member->id)->update(['status' => 'closed']);
            }
        } // member exists
    }
    
}
