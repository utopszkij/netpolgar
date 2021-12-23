<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $result->memberCount = \DB::table('members')->where('parent_type','=', $poll->parent_type)
            ->where('parent','=', $poll->parent)->where('status','=','active')->count();
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
     * like szám lapján szükség szerint status modositás
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
                if (time() > $debateEnd) {
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
}
