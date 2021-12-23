<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_id', 'name', 'status', 'description',
        'created_by'
    ];

	 /**
	 * kiegészítő infók lekérése    
	 * @param Option $option
	 * @return objeckt {likeCount, likeReq, userLiked}
	 */ 
	 public function getInfo($option) {
			$result = JSON_decode('{
				"likeCount":0, 
				"likeReq":0, 
				"userLiked":false}');
			$poll = \DB::table('polls')->where('id','=',$option->poll_id)
				->first();
			$poll->config = JSON_decode($poll->config);
			if (!isset($poll->config->optionActivate)) {
				$poll->config->optionActivate = 2;			
			}
			$t = \DB::table('members');	
			$memberCount = $t->select('distinct user_id')
        ->where('parent_type','=',$poll->parent_type)
        ->where('parent','=',$poll->parent)
        ->where('status','=','active')
        ->count();
			$result->likeReq = round(($memberCount * $poll->config->optionActivate) / 100);	
			$user = \Auth::user();
			if ($user) {
				$result->likeCount = \DB::table('likes')
				->where('parent_type','=','options')
				->where('parent','=',$option->id)
				->where('like_type','=','like')
				->count();
				$result->userLiked = (\Db::table('likes')
				->where('parent_type','=','options')
				->where('parent','=',$option->id)
				->where('like_type','=','like')
				->where('user_id','=',$user->id)
				->count() > 0);
			} 
			return $result;		 
	 }	    
    
    /**        
     * like szám lapján szükség szerint status modositás
     * @param string $optionId
     * @result string new status
     */
    public function checkStatus(string $optionId):string {
    	$result = '';
    	$option = $this->where('id','=',$optionId)->first();
    	if ($option) {
	    	if ($option->status == 'proposal') {
				$info = $this->getInfo($option);
				if ($info->likeCount >= $info->likeReq) {
					$result = 'active';
					$this->where('id','=',$optionId)
							->update(['status' => $result]);				
				}    	
	    	}
   	}
    	return $result;
    }	

}
