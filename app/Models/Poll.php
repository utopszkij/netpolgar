<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Rules\LiquedRule;


class Poll extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'name', 'status', 'description',
        'config', 'created_by'
    ];
    
    /**
     * Üres poll rekord
     * @return object
     */
    public static function emptyRecord() {
        $result = JSON_decode('{
			"id":0,
            "parent_type":"", 
            "parent":0, 
            "name":"", 
            "status":"proposal", 
            "description":"",
            "debate_start":"1900-01-01",
            "config":{
                "pollType":"pref",
                "secret":false,
                "liquied":true,
                "debateStart":60,
                "optionActivate":60,
                "debateDays":10,
                "voteDays":10,
                "valid":30
            },
            "created_by":0,
            "created_at":"",
            "updated_at":""
	   }');
        $result->created_at = date('Y-m-d H:i');
        $result->updated_at = date('Y-m-d H:i');
        return $result;
    }
    
    /**
     * bejelentkezett userre vonatkozó kiegészitő infók lekérése
     * @param object $result
     * @param Poll $poll
     * @return void $result modosul
     */
    public static function getUserInfo(&$result, $poll):void {
        $user = \Auth::user();
        $model = new \App\Models\Like();
        if ($user) {
            $result->userLiked = ($model->where('parent_type','=','polls')
                ->where('parent','=', $poll->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() > 0);
            $result->userMember = (\DB::table('members')
                ->where('parent_type','=', $poll->parent_type)
                ->where('parent','=', $poll->parent)
                ->where('user_id','=',$user->id)
                ->where('status','=','active')
                ->count() > 0
                );
            $result->userAdmin = (\DB::table('members')
                ->where('parent_type','=', $poll->parent_type)
                ->where('parent','=', $poll->parent)
                ->where('user_id','=',$user->id)
                ->where('rank','=','admin')
                ->where('status','=','active')
                ->count() > 0
                );
            if ($poll->created_by == $user->id) {
                $result->userAdmin = true;
            }
            if ($poll->config->secret) {
                $result->userVoted = (\DB::table('ballots')
                    ->where('poll_id','=',$poll->id)
                    ->where('user_id','=',$user->id)
                    ->count() <= 0);
            } else {
                $result->userVoted = ( \DB::table('votes')
                    ->where('poll_id','=',$poll->id)
                    ->where('user_id','=',$user->id)
                    ->count() > 0); 
            }
        }
    }
    
    /**
     * kiegészitő poll információk lekérése
     * @param Poll $poll
     * @return object
     */
    public static function getInfo($poll) {
        $result = JSON_decode('{
            "creator":"",
            "userVoted":false,
            "likeCount":0,
            "userLiked":false,
            "likeReq":0,
            "userMember":false,
            "userAdmin":false,
            "userVoted":false,
            "memberCount":0,
            "voteCount":0
        }');
        $creatorUser = \DB::table('users')->where('id','=',$poll->created_by)->first();
        if ($creatorUser) {
            $result->creator = $creatorUser->name;
        }
        $model = new \App\Models\Like();
        $result->likeCount = $model->where('parent_type','=','polls')
            ->where('parent','=', $poll->id)->where('like_type','=','like')->count();
        $result->memberCount = \DB::table('members')
        		->where('parent_type','=', $poll->parent_type)
            ->where('parent','=', $poll->parent)
            ->where('status','=','active')
            ->count();
        $result->voteCount = count((\DB::table('votes')
            						->select('ballot_id')
            						->where('poll_id','=', $poll->id)
            						->groupBy('ballot_id')->get()));
        $result->likeReq = round($poll->config->debateStart * $result->memberCount / 100);
        $result->likeReq = min($result->likeReq, $result->memberCount);
        Poll::getUserInfo($result, $poll);
        return $result;
    }
    
    /**        
     * like szám és dátum lapján szükség szerint status modositás
     * szükség esetén ballot rekordok generálása
     * @param string $pollId
     * @result string new status
     */
    public function checkStatus(string $pollId):string {
        $poll = \DB::table('polls')->where('id','=',$pollId)->first();
        $result = $poll->status;
        if ($poll) {
            $poll->config = JSON_decode($poll->config);
            if ($poll->status == 'proposal') {
                $info = $this->getInfo($poll);
                if ($info->likeCount >= $info->likeReq) {
                    \DB::table('polls')->where('id','=',$pollId)->update([
                        "status" => "debate",
                        "debate_start" => date('Y-m-d')
                    ]);
                    $result = 'debate';
                }
            }
            if ($poll->status == 'debate') {
                $debateStart = strtotime($poll->debate_start);
                $debateEnd = strtotime("+".$poll->config->debateDays." day", $debateStart);
                // van érvényes opció?
                $options = \DB::table('options')
                ->where('poll_id','=',$poll->id)
                ->where('status','=','activate')
                ->get();
                if ((time() > $debateEnd) & (count($options) > 0)) {
                    \DB::table('polls')->where('id','=',$pollId)->update([
                        "status" => "vote"]);
                    // ballotok generálása
                    \DB::statement('insert into ballots (poll_id, user_id)
                    select '.$pollId.', user_id
                    from members
                    left outer join users on users.id = members.user_id
                    where members.parent_type = "'.$poll->parent_type.'" and
                          members.parent = "'.$poll->parent.'" and
                          members.status = "active"
                          group by user_id
                          order by users.password
                    ');
                    $result = 'vote';
                }
            }
            if ($poll->status == 'vote') {
                $debateStart = strtotime($poll->debate_start);
                $voteEnd = strtotime("+".($poll->config->debateDays + $poll->config->voteDays)." day", 
                                     $debateStart);
                if (time() > $voteEnd) {
                    \DB::table('polls')->where('id','=',$pollId)->update([
                        "status" => "closed"]);
                    $result = 'closed';
                }
            }
        }
        return $result;
    }
    
        /**
     * Bejelntkezett user tag a parent -ben?
     * @param string $parentType
     * @param string $parent
     * @return bool
     */
    public static function userMember(string $parentType, int $parentId):bool {
        if (\Auth::user()) {
            $result = (\DB::table('members')
                ->where('parent_type','=',$parentType)
                ->where('parent','=',$parentId)
                ->where('user_id','=',\Auth::user()->id)
                ->where('status','=','active')
                ->count() > 0);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Bejelntkezett user admin ebben a szavazásban?
     * @param Poll $poll
     * @return bool
     */
    public static function userAdmin($poll):bool {
        if (\Auth::user()) {
            $result = (\DB::table('members')
                ->where('parent_type','=',$poll->parent_type)
                ->where('parent','=',$poll->parent)
                ->where('user_id','=',\Auth::user()->id)
                ->where('rank','=','admin')
                ->where('status','=','active')
                ->count() > 0);
            if (\Auth::user()->id == $poll->created_by) {
            	$result = true;
            }    
        } else {
            $result = false;
        }
        return $result;
    }

	/**
	 * lapozható adat objekt lekérése az adatbázisból
	 * szükség esetén a poll.status értékeket is modositja (ilyenkor újra kell olvasni)
	 * @param string $parentType
	 * @param int $parentId
	 * @param string $statues  lista
	 * @param int $pageSize
	 * @return object
	 */ 
	public function getData(string $parentType, int $parentId,
		string $statuses, int $pageSize) {	  
		  $updated = true;
		  $counter = 0;
		  while (($updated) & ($counter < 20)) {	
	        $data = $this->latest()
	        ->where('parent_type','=',$parentType)
	        ->where('parent','=',$parentId)
	        ->whereIn('status', explode('-',$statuses))
	        ->orderBy('created_at')
	        ->paginate(8);
	        $updated = false;
	        foreach ($data as $item) {
	        	  $oldStatus = $item->status;	
	           $item->status = $this->checkStatus($item->id);
	           if ($item->status != $oldStatus) {
						$updated = true;           
	           }
	        }
	        $counter++;
        }
        return $data;
    }   

    /**
     * poll rekord irása az adatbázisba a $request-be lévő információkból
     * @param int $id
     * @param Request $request
     * @return string, $id created new record id
     */
    public static Function saveOrStore(int &$id, Request $request): string {
        $parentType = $request->input('parent_type');
        $parent = $request->input('parent');
            
        // rekord array kialakitása
        $pollArr = [];
        $pollArr['parent_type'] = $request->input('parent_type');
        $pollArr['parent'] = $request->input('parent');
        $pollArr['name'] = strip_tags($request->input('name'));
        $pollArr['description'] = strip_tags($request->input('description'));
        if ($id == 0) {
            $pollArr['status'] = 'proposal';
            if (\Auth::user()) {
                $pollArr['created_by'] = \Auth::user()->id;
            } else {
                $pollArr['created_by'] = 0;
            }
        }
        
        // config kialakitása
        $config = new \stdClass();
        $config->pollType = $request->input('pollType');
        $config->secret = $request->input('secret');
        $config->liquied = $request->input('liquied');
        $config->debateStart = $request->input('debateStart');
        $config->optionActivate = $request->input('optionActivate');
        $config->debateDays = $request->input('debateDays');
        $config->voteDays = $request->input('voteDays');
        $config->valid = $request->input('valid');
        $pollArr['config'] = JSON_encode($config);
        
        // poll rekord tárolás az adatbázisba
        $errorInfo = '';
        try {
            $model = new Poll();
            if ($id == 0) {
                $pollRec = $model->create($pollArr);
                $id = $pollRec->id;
				// like rekord felvitele
				\DB::table('likes')->insert([
					"parent_type" => "polls",
					"parent" => $id,
					"user_id" => \Auth::user()->id,
					"like_type" => "like",
					"updated_at" => date('Y-m-d')
				]);
                $this->checkStatus($id);
            } else {
                $model->where('id','=',$id)->update($pollArr);
            }
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = $exception->errorInfo;
        }
        return $errorInfo;
    }

	/**
	 * parent olvasása az adatbázisból
	 * @param string $parentType
	 * @param int $parentId
	 * @retrun object | false
	 */ 
	public static function getParent(string $parentType, int $parentId) {	
       $result = \DB::table($parentType)->where('id','=',$parentId)->first();
       if (!$result) {
		 echo 'Fatal error parent not found'; exit(); 
	   }
       return $result;
	}
	
	/**
	 * Request -ben lévő adatok valid Poll rekord?
	 * @param Request $request
	 * @return bool
	 */ 
	public function valid(Request $request): bool {    
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'liquied' => new LiquedRule(),
            'debateStart' => ['numeric','min:0','max:100'],
            'optionActivate' => ['numeric','min:0','max:100'],
            'debateDays' => ['numeric','min:0','max:500'],
            'voteDays' => ['numeric','min:0','max:500'],
            'valid' => ['numeric','min:0','max:100']
        ]);
        // ide csak akkor jut a vezérlés ha minden OK, egyébként redirekt az elöző oldalra
        return true;
    }    

	/**
	 * YesNo opciók felvitele
	 * @param int $id
	 * @return void
	 */ 
	public static function addYesNoOptions(int $id) {			
		$optionsTable = \DB::table('options');
		$optionsTable->insert([
			"poll_id" => $id, 
			"name" => __('poll.yes'), 
			"description" => "", 
			"status" => "active",
			"created_by" => \Auth::user()->id			
		]);        
		$optionsTable->insert([
			"poll_id" => $id, 
			"name" => __('poll.no'), 
			"description" => "", 
			"status" => "active",
			"created_by" => \Auth::user()->id			
		]);        
	}			


}
