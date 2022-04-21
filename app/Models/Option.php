<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
			$result->likeCount = \DB::table('likes')
				->where('parent_type','=','options')
				->where('parent','=',$option->id)
				->where('like_type','=','like')
				->count();
			$result->likeReq = round(($memberCount * $poll->config->optionActivate) / 100);	
			$user = \Auth::user();
			if ($user) {
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
	 * poll kiegészitő információk lekérése
	 * @param poll record $poll
	 * @return object {userMember, userAdmin}
	 */
	 public static function getPollInfo($poll) {
	 	$result = JSON_decode('{"userMember":false, "userAdmin":false}');
	 	$user = \Auth::user();
	 	if ($user) {
		 	$result->userMember = (\DB::table('members')
		 		->where('parent_type','=',$poll->parent_type)
		 		->where('parent','=',$poll->parent)
		 		->where('user_id','=',$user->id)
		 		->where('status','=','active')->count() > 0);
		 	$result->userAdmin = (\DB::table('members')
		 		->where('parent_type','=',$poll->parent_type)
		 		->where('parent','=',$poll->parent)
		 		->where('user_id','=',$user->id)
		 		->where('rank','=','admin')
		 		->where('status','=','active')->count() > 0);
		 	if ($user->id == $poll->created_by) {
				$result->userAdmin = true;		 	
		 	}	
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

	/** poll parent beolvasása
	 * @param Poll $poll
	 * @return object|false
	 */ 
 	public static function getPollParent($poll) {	
  		return \DB::table($poll->parent_type)
  		->where('id','=',$poll->parent)->first();
  	}		

	public function updateOrCreate(Request $request):string {
		$errorInfo = '';
		if (\Auth::check()) {
			try {
				if ($request->input('optionId',0) == 0) {
						$newOption = $this->create([
							'poll_id' => $request->input('pollId'),
							'name' => mb_substr(strip_tags($request->input('description')),0,80),
							'description' => strip_tags($request->input('description')),
							'status' => 'proposal',
							'created_by' => \Auth::user()->id				
						]);
						// like rekord felvitele
						\DB::table('likes')->insert([
							"parent_type" => "options",
							"parent" => $newOption->id,
							"user_id" => \Auth::user()->id,
							"like_type" => "like",
							"updated_at" => date('Y-m-d')
						]);
						$this->checkStatus($newOption->id);
				} else {
					$old = $this->where('id','=',$request->input('optionId'))->first();
					if ($old->description == '') {
						$old->description = $old->name;
					}
					$newDescription = strip_tags($request->input('description'));
					$name = mb_substr($newDescription,0,80);
					$log = \App\Models\Minimarkdown::buildLog($old->description, $newDescription);	
					$this->checkStatus($request->input('optionId'));
					if ($log != '') {
						$newDescription .= '{log}'.$log;
					}
					$this->where('id','=',$request->input('optionId'))
					->update([
						'description' => $newDescription,
						'name' => $name
					]);
				}
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = JSON_encode($exception->errorInfo);
			}	
		} else {
			$errorInfo = 'not logged';
		}
		return $errorInfo;
	}

}
