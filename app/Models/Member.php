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
    
}
