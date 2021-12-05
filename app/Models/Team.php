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
    
    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Team $team
     * @return void  $result -ot modosítja
     */
    public static function getLikeInfo(&$result, $team): void {
        // like, disLike, memberCount infok
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','teams')
        ->where('parent','=',$team->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','teams')
        ->where('parent','=',$team->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','teams')
                ->where('parent','=',$team->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','teams')
                ->where('parent','=',$team->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$team->id)
        ->count();
        $config = JSON_decode($team->config);
        if (!isset($config->close)) {
            $config->close = 98;
        }
        if (!isset($config->subTeamActivate)) {
            $config->subTeamActivate = 2;
        }
        $result->likeReq = $config->subTeamActivate;
        $result->disLikeReq = round($config->close * $result->memberCount / 100);
    }
        
    /**
     * taam tulajdonosok lekérése
     * @param object $result
     * @param Team $team
     * @return void
     */
    public static function getPath(&$result, $team): void {
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
        return;
    }
    
    /**
     * userRank és userParentRank kigyüjtése
     * @param object $result
     * @param Team $team
     * @return void   $result modosítása
     */
    public static function getRanks(&$result, $team) {
        if (\Auth::user()) {
            $t = \DB::Table('members');
            $items = $t->where('parent','=',$team->id)
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
			"disLikeReq":0,
            "meberCount":0
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
		Team::getLikeInfo($result, $team);
		$result->status = $team->status;
		Team::getPath($result, $team);
        Team::getRanks($result, $team);
		return $result;
    }
    
    /**
     * like/dilike ellenörzés, ha szülséges status modosítás
     * @param string $teamId
     * @return void
     */
    public function checkStatus(string $teamId):void {
        $model = new \App\Models\Team();
        $team = $model->where('id','=',$teamId)->first();
        if ($team) {
            $info = JSON_decode('{}');
            $this->getLikeInfo($info, $team);
            if (($team->status == 'proposal') & ($info->likeCount >= $info->likeReq)) {
                $model->where('id','=',$team->id)->update(['status' => 'active']);
            }
            if (($team->status == 'active') & ($info->disLikeCount >= $info->disLikeReq)) {
                $model->where('id','=',$team->id)->update(['status' => 'closed']);
            }
        }
    }
}


