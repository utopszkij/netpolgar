<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\Rules\RanksRule;
use App\Models\Minimarkdown;

if (!defined('UNITTEST')) {
	define('UNITTEST',1);
}

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'created_by', 'description', 'team_id', 
        'avatar', 'deadline', 'config', 'status'
    ];
    
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"parent":0,
			"description":"",
			"avatar":"/img/project.png",
			"deadline":"'.date('Y-m-d').'",
			"status":"proposal",
			"config":{
				"ranks":["admin","president","manager","moderator"],
				"close":98,
				"memberActivate":2,
				"memberExclude":95,
				"rankActivate":40,
				"rankClose":95,
				"debateActivate":2
			},
			"activated_at":"1900-01-01",
			"created_at":"1900-01-01",
			"closed_at":"1900-01-01",
			"updated_at":"1900-01-01",
			"created_by":0,
			"userAdmin":false,
			"userMember":false
    	}');
    	return $result;
    }

    
    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Project $project
     * @return void  $result -ot modosítja
     */
    public static function getLikeInfo(&$result, $project): void {
        // like, disLike, memberCount infok
        $parent = \DB::table('teams')->where('id','=',$project->team_id)->first();
        if (!$parent) {
				echo 'Fatal error parent not found'; exit();        
        }
        $parent->config = JSON_decode($parent->config);
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','projects')
        ->where('parent','=',$project->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','projects')
        ->where('parent','=',$project->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','projects')
                ->where('parent','=',$project->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','projects')
                ->where('parent','=',$project->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','projects')
        ->where('parent','=',$project->id)
        ->where('status','=','active')
        ->count();
        $result->parentMemberCount = $t->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$project->team_id)
        ->where('status','=','active')
        ->count();
        if (is_string($project->config)) {
        		$config = JSON_decode($project->config);
        } else {
				$config = $project->config;        
        }		
        if (!isset($config->close)) {
            $config->close = 98;
        }
        $result->likeReq = $parent->config->projectActivate;
        if ($result->likeReq > $result->parentMemberCount) {
            $result->likeReq = $result->parentMemberCount;
        }
        $result->disLikeReq = round($config->close * $result->memberCount / 100);
        if ($result->disLikeReq > $result->memberCount) {
            $result->disLikeReq = $result->memberCount;
        }
    }
    
    /**
     * userRank és userParentRank kigyüjtése
     * @param object $result
     * @param Team $team
     * @return void   $result modosítása
     */
    public static function getRanks(&$result, $project) {
        if (\Auth::user()) {
            $t = \DB::Table('members');
            $items = $t->where('parent','=',$project->id)
            ->where('parent_type','=','projects')
            ->where('user_id','=',\Auth::user()->id)
            ->get();
            foreach ($items as $item) {
                $result->userRank[] = $item->status.'_'.$item->rank;
            }
            
        }
    }
    
	 public static function getInfoFromTeam($team) {
		$result = JSON_decode('{
			"status":"active",
			"userRank":[],
			"userLiked":false,
			"userDisLiked":false,
			"likeCount":0,
			"likeReq":0,
			"disLikeCount":0,
			"disLikeReq":0,
         "memberCount":0,
         "userParentRank":[],
			"parentClosed":false,        
         "parentMemberCount":0,
         "userMember":false,
         "userAdmin":false
		}');
		$result->status = 'active';
		if ($team->status == 'closed') {
			$result->parentClosed = true;		
		}
      $result->parentMemberCount = \DB::table('members')
        ->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$team->id)
        ->where('status','=','active')
        ->count();
      $t = \DB::Table('members');
      if (\Auth::user()) {
	      $items = $t->where('parent','=',$team->id)
	            ->where('parent_type','=','teams')
	            ->where('user_id','=',\Auth::user()->id)
	            ->get();
	      foreach ($items as $item) {
	         $result->userParentRank[] = $item->status.'_'.$item->rank;
	      }
      }
		return $result;
	 }    
    
    public static function getInfo($project) {
    	// parentClosed ha a tulajdonos team closed
    	// userRank [ 'active_member', 'proposal_admin', ...]
		$t = \DB::Table('teams');    		
		$team = $t->where('id','=',$project->team_id)->first();
		if (!$team) {
		    echo 'Fatal error team not found'; exit();
		}
		$result = Project::getInfoFromTeam($team);
		$result->accredited = false;
		Project::getLikeInfo($result, $project);
		$result->status = $project->status;
      Project::getRanks($result, $project);
      $user = \Auth::user();
      if ($user) {
			$result->userMember = (\DB::table('members')
											->where('parent_type','=','projects')
											->where('parent','=',$project->id)
											->where('user_id','=',$user->id)
											->where('status','=','active')
											->count() > 0);
			$result->userAdmin = (\DB::table('members')
											->where('parent_type','=','projects')
											->where('parent','=',$project->id)
											->where('user_id','=',$user->id)
											->where('rank','=','admin')
											->where('status','=','active')
											->count() > 0);
			// az első user is admin
			$firstUser = \DB::table('users')
							->orderBy('id')
							->first();
			if ($firstUser->id == $user->id) {
				$result->userAdmin = true;			
				$result->userMember = true;			
			}																		      
      }
      if (\Auth::check()) {
          $accredited = \DB::table('likes')
          ->select('users.name','users.profile_photo_path','users.email')
          ->join('members','members.id','likes.parent')
          ->join('users','users.id','members.user_id')
          ->where('likes.parent_type','=','members')
          ->where('likes.user_id','=',\Auth::user()->id)
          ->where('members.parent_type','=','projects')
          ->where('members.parent','=',$project->id)
          ->where('members.rank','=','accredited')
          ->first();
          if ($accredited) {
              $result->accredited = $accredited;
          }
      }
      return $result;
    }
    
    /**
     * like/dislike ellenörzés, ha szülséges status modosítás
     * @param string $projectId
     * @return void
     */
    public function checkStatus(string $projectId):void {
        $model = new \App\Models\Project();
        $project = $model->where('id','=',$projectId)->first();
        if ($project) {
            $info = JSON_decode('{}');
            $this->getLikeInfo($info, $project);
            if (($project->status == 'proposal') & ($info->likeCount >= $info->likeReq)) {
                $model->where('id','=',$project->id)->update(['status' => 'active']);
            }
            if (($project->status == 'active') & ($info->disLikeCount >= $info->disLikeReq)) {
                $model->where('id','=',$project->id)->update(['status' => 'closed']);
            }
        }
    }
    
    /**
	 * project rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 public static function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			$projectArr = [];
			$projectArr['team_id'] = $request->input('team_id');
			$projectArr['name'] = strip_tags($request->input('name'));
			$projectArr['description'] = strip_tags($request->input('description'));
			$projectArr['avatar'] = strip_tags($request->input('avatar'));
			$fileInfo = Minimarkdown::getRemoteFileInfo($projectArr['avatar']);
			if (($fileInfo['fileSize'] > 2000000) |
			    ($fileInfo['fileSize'] < 10)) {
				$projectArr['avatar'] = '/img/noimage.png';
			} 

			$projectArr['deadline'] = $request->input('deadline');
			if ($id == 0) {
				$projectArr['status'] = 'proposal';
				if (\Auth::check()) {
					$projectArr['created_by'] = \Auth::user()->id;
				} else {
					$projectArr['created_by'] = 0;
				}		
			}
			   
			// config kialakitása
			$config = new \stdClass();
			$config->ranks = explode(',',$request->input('ranks'));
			$config->close = $request->input('close');
			$config->memberActivate = $request->input('memberActivate');
			$config->memberExclude = $request->input('memberExclude');
			$config->rankActivate = $request->input('rankActivate');
			$config->rankClose = $request->input('rankClose');
			$config->debateActivate = $request->input('debateActivate');
			$projectArr['config'] = JSON_encode($config);

			// project rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
				$model = new Project();
				if ($id == 0) {
			 		$projectRec = $model->create($projectArr);
			 		$id = $projectRec->id;
			 	} else {
					$model->where('id','=',$id)->update($projectArr);			 	
			 	}	
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = JSON_encode($exception->errorInfo);
			}	
			return $errorInfo;		
	 }	
	 
	 
	 /**
	 * bejelentkezett user legyen admin -ja az $id project -nek
	 * @param int $id
	 * @return string
	 */
     public static function addAdmin(int $id): string {	
		if (!\Auth::check()) {
			return '';
		}	
		$memberArr = [];
		$memberArr['parent_type'] = 'projects';
		$memberArr['parent'] = $id;
		$memberArr['user_id'] = \Auth::user()->id;
		$memberArr['rank'] = 'admin';
		$memberArr['status'] = 'active';
		$memberArr['created_by'] = \Auth::user()->id;
		$errorInfo = '';
		try {
			Member::create($memberArr);
		} catch (\Illuminate\Database\QueryException $exception) {
		    $errorInfo = JSON_encode($exception->errorInfo);
		}
		return $errorInfo;			
	 }		

	 public static function valid(Request $request): bool {
			// tartalmi ellenörzések 
			if (!defined('UNITTEST')) {
			$request->validate([
					'name' => 'required',
					'ranks' => ['required', new RanksRule()],
					'description' => 'required',
					'deadline' => ['required','date_format:Y-m-d']
			]);
			}
			return true; // ide csak akkor kerül ha minden OK
	 }
	 
	 public static function getData($teamId,$pageSize) {	
        return \DB::table('projects')
        			 ->where('team_id','=',$teamId)
        			 ->orderBy('name')
        			 ->paginate($pageSize);
     }    			 

	 public static function getDataByUser($userId,$pageSize) {	
        return \DB::table('projects')
				->select('projects.id','projects.name','projects.avatar',
				'projects.description','projects.status',
				'projects.team_id','projects.config','projects.deadline')
				->leftJoin('members','members.parent','projects.id')
				->where('members.parent_type','=','projects')
        		->where('members.user_id','=',$userId)
        		->where('members.status','=','active')
        		->orderBy('name')
        		->paginate($pageSize);
     }    			 
	 

}


