<?php
/**
* Team Adat model public functions:
*   $teamRecord = emptyRecord() 	
*   $errorInfo = updateOrCreate($request)
*   $infoObject = getInfo($id)
*   $paginatorObject = getData($parent, $pageSize) 	static
*   checkStatus($id) 											static
*   $bool = valid($request)
*   decodeConfig(&$team)
*   adjustRegisteredTeamMembers()
*/

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request; 
use App\Rules\RanksRule;
use App\Models\Minimarkdown;

class Team extends Model {
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
				"ranks":["admin","president","manager","moderator","accredited"],
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
	 * team rekord irása az adatbázisba a $request-be lévő információkból
	 * @param Request $request
	 * @return string  
	 */	 
	 public function updateOrCreate(Request $request): string {
	 		$id = $request->input('id',0);	
			// rekord array kialakitása
			$teamArr = [];
			$teamArr['parent'] = $request->input('parent');
			$teamArr['name'] = strip_tags($request->input('name'));
			$teamArr['description'] = strip_tags($request->input('description'));
			$teamArr['avatar'] = strip_tags($request->input('avatar'));
			$fileInfo = Minimarkdown::getRemoteFileInfo($teamArr['avatar']);
			if (($fileInfo['fileSize'] > 2000000) |
			    ($fileInfo['fileSize'] < 10)) {
				$teamArr['avatar'] = '/img/noimage.png';
			} 
			
			if ($id == 0) {
				$teamArr['status'] = 'proposal';
				if (\Auth::user()) {
					$teamArr['created_by'] = \Auth::user()->id;
				} else {
					$teamArr['created_by'] = 0;
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
			$config->projectActivate = $request->input('projectActivate');
			$config->productActivate = $request->input('productActivate');
			$config->subTeamActivate = $request->input('subTeamActivate');
			$config->debateActivate = $request->input('debateActivate');
			$teamArr['config'] = JSON_encode($config);
			// teams rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
				$model = new Team();
				if ($id == 0) {
			 		$teamRec = $model->create($teamArr);
			 		$id = $teamRec->id;
			 		$errorInfo = $this->addAdmin($id);
					// like rekord felvitele
					\DB::table('likes')->insert([
							"parent_type" => "teams",
							"parent" => $teamRec->id,
							"user_id" => \Auth::user()->id,
							"like_type" => "like",
							"updated_at" => date('Y-m-d')
					]);
					$this->checkStatus($teamRec->id);
			 	} else {
					$model->where('id','=',$id)
					->update($teamArr);			 	
			 	}	
			 	// file upload kezelése
 			    $uploadMsg = Upload::processUpload('img',
								storage_path().'/teams/'.$id.'/',
								'avatar',
								['jpg','png','gif']);
				if ($uploadMsg != 'no upload') {
					if (substr($uploadMsg,0,5) != 'ERROR') {
						$avatarUrl = str_replace(storage_path(),\URL::to('/storage'),$uploadMsg);
						\DB::table('teams')
							->where('id','=',$id)
							->update(["avatar" => $avatarUrl]);
					} else {
						$errorInfo = $uploadMsg;
					}
				}
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = JSON_encode($exception->errorInfo);
			}	
			return $errorInfo;		
	 }	

	 /**
	 * kiegészítő infók kiolvasása
	 * @param int $id
	 * @return object
	 */
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
         "memberCount":0,
         "userMember": false,
         "userAdmin": false   
		}');
		if ($id == 0) {
			$result->status = 'active';
			if (\Auth::user()) {
				$result->userRank = ['active_member'];
				$result->userParentRank = ['active_member'];
				$result->userMember = true;
				$result->userAdmin = true;
			}	
			return $result;
		}
		$team = Team::where('id','=',$id)->first();
		if (!$team) {
		    return $result;
		}
		Team::getLikeInfo($result, $team);
		$result->status = $team->status;
		Team::getPath($result, $team);
      Team::getRanks($result, $team);
      $result->userMember = (in_array('active_member',$result->userRank) | 
	 	        in_array('active_admin',$result->userRank));
		$result->userAdmin = in_array('active_admin',$result->userRank);
		return $result;
    }
    
	 /**
	 * kiegészítő infók kiolvasása user szerinti lekéréshez
	 * @param int $userId
	 * @return object
	 */
    public static function getInfoByUser(int $userId) {
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
         "memberCount":0,
         "userMember": false,
         "userAdmin": false   
		}');
		$result->userMember = true; 
		if (\Auth::check()) {
			$result->userAdmin = ($userId == \Auth::user()->id);
		}	
		return $result;
    }
    

	 /**
	 * lapozható adat objekt kialakítása
	 * @param int $parent
	 * @param int $pageSize
	 * @return object
	 */
    public static function getData(int $parent, int $pageSize) {	
        return \DB::table('teams')
        			 ->where('parent','=',$parent)
        			 ->orderBy('name')
        			 ->paginate($pageSize);
    }  

	 /**
	 * lapozható adat objekt kialakítása userId szerinti lekéréshez
	 * @param int $userId
	 * @param int $pageSize
	 * @return object
	 */
    public static function getDataByUser(int $userId, int $pageSize) {	
        return \DB::table('teams')
			->select('teams.id','teams.name','teams.avatar',
				'teams.description','teams.status')
			->leftJoin('members','members.parent','teams.id')
			->where('members.parent_type','=','teams')
        	->where('members.user_id','=',$userId)
        	->where('members.status','=','active')
        	->orderBy('teams.name')
        	->paginate($pageSize);
    }  

    
    /**
     * like/dilike utáni ellenörzés, ha szülséges status modosítás
     * @param string $teamId
     * @return void
     */
    public function checkStatus(string $teamId):void {
        $team = $this->where('id','=',$teamId)->first();
        if ($team) {
            $info = JSON_decode('{}');
            $this->getLikeInfo($info, $team);
            if (($team->status == 'proposal') & ($info->likeCount >= $info->likeReq)) {
                $this->where('id','=',$team->id)
                ->update(['status' => 'active']);
            }
            if (($team->status == 'active') & ($info->disLikeCount >= $info->disLikeReq)) {
                $this->where('id','=',$team->id)
                ->update(['status' => 'closed']);
            }
        }
    }

  	/**
  	* Request valid Team record? (tárolás előtti ellenörzés)
  	* @param Request
  	* @return bool
  	*/		 
	public static function valid(Request $request):bool {
			$request->validate([
				'name' => 'required',
				'ranks' => ['required', new RanksRule()],
				'description' => 'required',
				'close' => ['required','numeric','min:0','max:100'],         
				'memberActivate' => ['required','numeric','min:0','max:100'],
				'memberExclude' => ['required','numeric','min:0','max:100'],
				'rankActivate' => ['required','numeric','min:0','max:100'],
				'rankClose' => ['required','numeric','min:0','max:100'],
				'projectActivate' => ['required','numeric','min:0','max:100'],
				'productActivate' => ['required','numeric','min:0','max:100'],
				'subTeamActivate' => ['required','numeric','min:0','max:100'],
				'debateActivate' => ['required','numeric','min:0','max:100']
			]);
			// ide csak akkor kerül a vezérlés ha minden OK
			// egyébként redirekt az elöző oldalra
			return true; 
	}

   /**
    * team->config json string dekodolása
    * @param Team $teamRec
    * @return void
    */      
    public function decodeConfig(Team &$teamRec) {
    	 try {	
    	  		$teamRec->config = JSON_decode($teamRec->config);
		 } catch(Exception $e) {
			   $emtyRec = $this->emptyRecord();
			   $teamRec->config = $emtyrec->config;
		 }	
    	 if (!isset($teamRec->config->ranks)) {
    	  		$teamRec->config->ranks = ['admin','manager','president','moderator'];
    	 } else if (is_string($teamRec->config->ranks)) {
				$teamRec->config->ranks = explode(',',$team->config->ranks);
    	 }
    }

 	 /**
	 * bejelentkezett user legyen admin -ja az $id team -nek
	 * @param int $id
	 * @return string
	 */
    protected function addAdmin(int $id): string {		
        $memberArr = [];
        $memberArr['parent_type'] = 'teams';
        $memberArr['parent'] = $id;
        $memberArr['user_id'] = \Auth::user()->id;
        $memberArr['rank'] = 'member';
        $memberArr['status'] = 'active';
        $memberArr['created_by'] = \Auth::user()->id;
        $errorInfo = '';
        try {
            Member::create($memberArr);
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = $exception->errorInfo;
        }
        if ($errorInfo == '') {
            $memberArr = [];
            $memberArr['parent_type'] = 'teams';
            $memberArr['parent'] = $id;
            $memberArr['user_id'] = \Auth::user()->id;
            $memberArr['rank'] = 'admin';
            $memberArr['status'] = 'active';
            $memberArr['created_by'] = \Auth::user()->id;
            $errorInfo = '';
            try {
                Member::create($memberArr);
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = $exception->errorInfo;
            }
        }
		return $errorInfo;			
	 }		
    
    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Team $team
     * @return void  $result -ot modosítja
     */
    protected function getLikeInfo(&$result, Team $team): void {
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
        ->where('status','=','active')
        ->count();
        $config = JSON_decode($team->config);
        if (!isset($config->close)) {
            $config->close = 98;
        }
        if (!isset($config->subTeamActivate)) {
            $config->subTeamActivate = 2;
        }
        $result->likeReq = $config->subTeamActivate;
        if ($result->likeReq > $result->memberCount) {
            $result->likeReq = $result->memberCount;
        }
        $result->disLikeReq = round($config->close * $result->memberCount / 100);
        if ($result->disLikeReq > $result->memberCount) {
            $result->disLikeReq = $result->memberCount;
        }
    }
        
    /**
     * taam parent path lekérése
     * @param object $result
     * @param Team $team
     * @return void
     */
    protected function getPath(&$result, Team $team): void {
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
    protected function getRanks(&$result, Team $team) {
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
    
    
	/**
	* minden regisztrált user legyen tagja az "1"-es teamnek
	*/	
   public function adjustRegisteredTeamMembers() {
       try {
          \DB::statement('insert into members (parent_type, parent, user_id, `status`, `rank`, created_by) 
            select "teams", 1, users.id, "active", "member", users.id
            from users
            left outer join members on members.parent_type = "teams" and
                                      members.parent = 1 and members.user_id = users.id
            where members.id is null
          ');  
       } catch (\Illuminate\Database\QueryException $exception) {
           echo JSON_encode($exception->errorInfo); exit();
       }
   }  
   
   /** treeItem json string kialakitása (rekurziv)
    * @param Tree $tree
    * @return string;
    */ 
   protected function getTreeItem($team): string {
		$result = '';
		if ($team) {
			$result = '{"id":'.$team->id;
			$url = "'".\URL::to('team/'.$team->id)."'";
			if ($team->status == 'active') {
				$result .= ', "text":"'.$team->name.'"';
			} else {
				$result .= ', "text":"('.$team->name.')"';
			}	
			
			$childrens = $this->where('parent','=',$team->id)
				->orderBy('name')
				->get();
			 
			if (count($childrens) > 0) {
				$i = 0;
				$result .= ', "children":[';
				foreach ($childrens as $children) {
					$result .= $this->getTreeItem($children);
					$i++;
					if ($i < count($childrens)) {
							$result .= ',';
					}
				}
				$result .= ']}';
			} else {
				$result .= '}';
			}	
		}
		return $result;
   }
   
   /**
    * adat lekérés fa strukturában
    * @return json string
    */	
   public function getTree(): string {
	   $result = '[{"id":0, "text": "root", "children":[';
	   $roots = $this->where('parent','=',0)->orderBy('name')->get();
	   $i = 0;
	   foreach ($roots as $root) {
		   $result .= $this->getTreeItem($root);
		   $i++;
		   if ($i < count($roots)) {
				$result .= ',';
		   }
	   }
	   $result .= ']}]';
	   return $result;
   }
   
   /**
    * user adminja a team -nek?
    * @param int $teamId
    * @para, int $userId
    * @return bool
    */ 
   public static function userAdmin(int $teamId, int $userId): bool {
       return \App\Models\Member::userAdmin('teams', $teamId, $userId);
   }

   /**
    * user tagja a team -nek?
    * @param int $teamId
    * @para, int $userId
    * @return bool
    */ 
   public static function userMember(int $teamId, int $userId): bool {
       return \App\Models\Member::userMember('teams', $teamId, $userId);
   }
    
}


