<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = ['parent_type', 'parent', 'reply_to', 
        'user_id', 'value', 'msg_type', 'moderated_by', 'moderator_info'];
    
    public $tree = [];
    
    function __construct() {
        parent::__construct();
        $this->tree = [];
    }
    
    /**
     * egy rekord olvasása az adatbázisból
     * @param int $id
     * @return object|false
     */ 
    public static function getRecord(int $id) {
		return Message::where('id','=',$id)->first();
	}
    
    /**
     * user avatar url képzése
     * @param string $profile_photo_path
     * @param string $email
     * @return string
     */
	protected function avatar($profile_photo_path, $email): string {
	    return \App\Models\Avatar::userAvatar($profile_photo_path, $email);
    }
   
    public function init() {
        $result = JSON_decode('{
            "parent_type":"",
            "parent":0,
            "user_id":0,
            "msg_type":"",
            "value":"",
            "created_at":"'.date('Y-m-d').'",
            "updated_at":"",
            "moderatorinfo":"",
            "moderated_by":"",
        }');
        return $result;
    }
    
    /**
     * Teljes üzenet fa beolvasása $this->tree -be [{id, level, reply_to, readed}, ...]
     * Rekurziv
     * @param string $parentType
     * @param int $parent
     * @param int $replyTo
     * @param int $level
     * @return int válaszok száma
     */
    public function getTreeItem(string $parentType,
        int $parent, int $replyTo, int $level):int {
            if (\Auth::user()) {
                $userId = \Auth::user()->id;
            } else {
                $userId = 0;
            }
            if ($replyTo > 0) {
                $recs = \DB::select('select messages.id, messages.reply_to, msgreads.id as readed
                from messages
                left outer join msgreads
                     on msgreads.msg_id = messages.id and
                        msgreads.user_id = '.$userId.'
                where messages.reply_to = '.$replyTo.' 
                order by messages.id ASC');
            } else if ($parentType == 'users') {
                $recs = \DB::select('select messages.id, messages.reply_to, msgreads.id as readed
                    from messages
                    left outer join msgreads
                         on msgreads.msg_id = messages.id and
                            msgreads.user_id = '.$userId.'
                    where messages.reply_to = '.$replyTo.' and
                          messages.parent_type = "'.$parentType.'" and
                          (messages.parent = '.$parent.' or messages.user_id = '.$parent.')
                    order by messages.id ASC');
            } else {
                $recs = \DB::select('select messages.id, messages.reply_to, msgreads.id as readed
                    from messages
                    left outer join msgreads
                         on msgreads.msg_id = messages.id and
                            msgreads.user_id = '.$userId.'
                    where messages.reply_to = '.$replyTo.' and
                    messages.parent_type = "'.$parentType.'" and
                    messages.parent = '.$parent.'
                    order by messages.id ASC');
            }
            
            foreach ($recs as $rec) {
                $i = count($this->tree);
                $this->tree[$i] = new \stdClass();
                $this->tree[$i]->id = $rec->id;
                $this->tree[$i]->replyTo = $rec->reply_to;
                $this->tree[$i]->level = $level;
                $this->tree[$i]->readed = ($rec->readed > 0);
                $this->tree[$i]->replyCount = $this->getTreeItem($parentType, $parent, $rec->id, ($level+1));
            }
            return count($recs);
    }
    
    /**
     * üzenet lista beolvasása $this->tree -be [{id, level, reply_to, readed}, ...]
     * @param string $parentType
     * @param int $parent
     * @param int $replyTo
     * @param int $level
     * @return elemek száma
     */
    public function getListItem(string $parentType,
        int $parent, int $replyTo, int $level):int {
            if (\Auth::user()) {
                $userId = \Auth::user()->id;
            } else {
                $userId = 0;
            }
            if ($replyTo > 0) {
                $recs = \DB::select('select messages.id, messages.reply_to, 0 as readed
                    from messages
                    where messages.reply_to = '.$replyTo.' 
                    order by messages.id ASC');
            } else if ($parentType == 'users') {
                $recs = \DB::select('select messages.id, messages.reply_to, 0 as readed
                    from messages
                    where messages.reply_to = '.$replyTo.' and
                          messages.parent_type = "'.$parentType.'" and
                          (messages.parent = '.$parent.' or messages.user_id = '.$parent.')
                    order by messages.id ASC');
                
            } else {
                $recs = \DB::select('select messages.id, messages.reply_to, 0 as readed
                    from messages
                    where messages.reply_to = '.$replyTo.' and
                          messages.parent_type = "'.$parentType.'" and
                          messages.parent = '.$parent.'
                    order by messages.id ASC');
            }
            foreach ($recs as $rec) {
                $i = count($this->tree);
                $this->tree[$i] = new \stdClass();
                $this->tree[$i]->id = $rec->id;
                $this->tree[$i]->replyTo = $rec->reply_to;
                $this->tree[$i]->level = $level;
                $this->tree[$i]->readed = ($rec->readed > 0);
                $this->tree[$i]->replyCount = 
                    \DB::table('messages')
                    ->where('parent_type','=', $parentType)
                    ->where('parent','=', $parent)
                    ->where('reply_to','=', $rec->id)
                    ->count();
            }
            return count($recs);
    }
    
    
    /**
     * $rec messages információk kiegészítése
     * @param object $rec input: {id, ...} 
     * output: {id, userId, creatorUser, avatar, replyTo[id,name], time, text, stile
     *   replyCount, likeCount, disLikeCount, userLiked, userDisLiked, likeStyle, disLikeStyle} 
     */
    public function getInfo(&$rec) {
            if ($rec->id > 0) {
                $message = $this->getRecord($rec->id);
                if ($message) {
                    $rec->userId = $message->user_id;
                    $creatorUser = \DB::table('users')
                    ->where('id','=',$message->user_id)
                    ->first();
                    if ($creatorUser) {
                        $rec->user_id = $creatorUser->id;
                        $rec->creator = $creatorUser->name;
                        $rec->avatar = $this->avatar($creatorUser->profile_photo_path, $creatorUser->email);
                    } else {
                        $rec->user_id = 0;
                        $rec->creator = '?';
                        $rec->avatar = '?';
                    }
                    $rec->replyTo = [$message->reply_to,'',0];
                    
                    if (($message->parent_type == 'users') &
                        ($message->reply_to == 0)) {
                            $replyUser = \DB::table('users')->where('id','=',$message->parent)->first();
                            if ($replyUser) {
                                $rec->replyTo[1] = $replyUser->name.':';
                                $rec->replyTo[2] = $replyUser->id;
                            }
                    } else if ($message->reply_to > 0) {
                        $replyMessage = \DB::table('messages')
                        ->where('id','=',$message->reply_to)
                        ->first();
                        if ($replyMessage) {
                            $replyUser = \DB::table('users')
                            ->where('id','=',$replyMessage->user_id)
                            ->first();
                            if ($replyUser) {
                                $rec->replyTo[1] = $replyUser->name.':';
                                $rec->replyTo[2] = $replyUser->id;
                            }
                        }
                    }

                    $rec->time = $message->created_at;
                    $rec->text = $message->value;
                    $rec->style = $message->msg_type;
                    $rec->moderatorInfo = $message->moderator_info;
                    
                    $rec->likeCount = \DB::table('likes')
                    ->where('parent_type','=','messages')
                    ->where('parent','=',$message->id)
                    ->where('like_type','=','like')
                    ->count();
                    $rec->disLikeCount = \DB::table('likes')
                    ->where('parent_type','=','messages')
                    ->where('parent','=',$message->id)
                    ->where('like_type','=','dislike')            
                    ->count();
                    $rec->userLiked = false;
                    $rec->userDisLiked = false;
                    $rec->likeStyle = '';
                    $rec->disLikeStyle = '';
                    $user = \Auth::user();
                    if ($user) {
                        $rec->userLiked = (\DB::table('likes')
                            ->where('parent_type','=','messages')
                            ->where('parent','=',$message->id)
                            ->where('user_id','=',$user->id)
                            ->where('like_type','=','like')
                            ->count() > 0);
                        $rec->userDisLiked = (\DB::table('likes')
                            ->where('parent_type','=','messages')
                            ->where('parent','=',$message->id)
                            ->where('user_id','=',$user->id)
                            ->where('like_type','=','dislike')
                            ->count() > 0);
                    }
                    if ($rec->userLiked) {
                        $rec->likeStyle = 'strong';
                    }
                    if ($rec->userDisLiked) {
                        $rec->disLikeStyle = 'strong';
                    }
                    if (!isset($rec->replyCount)) {
                        $rec->replyCount =
                            \DB::table('messages')
                            ->where('parent_type','=', $message->parent_type)
                            ->where('parent','=', $message->parent)
                            ->where('reply_to','=', $message->id)
                            ->count();
                    }
                } // message
            } else { // if id > 0 
                $rec->replyTo = [$rec->replyTo,'',0];
            }
    }
    
    /**
     * az olvasatlan elemek előzményeit is olvasattlanra állítja
     * @param array $tree
     * @return void
     */
    public function setPathNotReaded() {
        for ($i=0; $i < count($this->tree); $i++) {
            if (!$this->tree[$i]->readed) {
                for ($j=$i-1; $j >= 0;  $j--) {
                    if ($this->tree[$j]->level < $this->tree[$i]->level) {
                        $this->tree[$j]->readed = false;
                    }
                    if ($this->tree[$j]->level == 0) {
                        $j = 0; // exit for
                    }
                }
            }
        }
    }
    
    /**
     * olvasott elemek száma
     * @param array $tree
     * @return int
     */
    protected function readedCount() {
        $result = 0;
        for ($i=0; $i < count($this->tree); $i++) {
            if ($this->tree[$i]->readed) {
                $result++;
            }
        }
        return $result;
    }
    
    /**
     * első olvasott elem törlése vagy 'excluded' -re cserélése
     * @param array $tree
     * @return void
     */
    protected function delFirstReaded() {
        $i = 0;
        
        // első olvasott elem keresése
        $item = false;
        while (($i < count($this->tree)) & (!$item)) {
            if ($this->tree[$i]->readed) {
                $item = $this->tree[$i];
            } else {
                $i++;
            }
        }
        
        if ($item) {
            // közvetlen és közvetett gyermekeinek megszámolása és törlése
            if ($i == (count($this->tree) - 1)) {
                $db = 0;
            } else {
                $j = $i + 1;
                while (($j < count($this->tree)) & ($this->tree[$j]->level > $item->level)) {
                    $j++;
                }
                $db = $j - $i - 1; // ennyi gyermeke van
                // közvetlen és közvetett gyermekeinek törlése
                array_splice($this->tree, $i + 1, $db);
            }
                        
            // a $item elem törlése vagy "excluded" jelzése
            if ($i > 0) {
                if ($this->tree[$i-1]->id < 0) {
                    array_splice($this->tree, $i, 1);
                } else {
                    $this->tree[$i]->id = -1; // ez jelzi, hogy "excluded"
                    $this->tree[$i]->readed = false;
                }
            } else {
                $this->tree[$i]->id = -1; // ez jelzi, hogy "excluded"
                $this->tree[$i]->readed = false;
            }
        }
    }
    
    /**
     * fa méret csökkentése
     */
    public function treeTruncate() {
        $i = 0;
        while ((count($this->tree) > 100) & ($this->readedCount() > 0) & ($i < 50)) {
            $this->delFirstReaded();
            $i++;
        }
    }
    
    public static function isModerator(string $parentType, int $parentId): bool {
        $result = false;
        if (\Auth::user()) {
            if ($parentType == 'teams') {
                $member = \DB::table('members')
                ->where('parent','=',$parentId)
                ->where('parent_type','=',$parentType)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',["moderator","admin"])
                ->where('status','=','active')
                ->orderBy('rank','asc')
                ->first();
                if ($member) {
                    $result = true;
                }
            }
            if ($parentType == 'polls') {
            	 $poll = \DB::table('polls')
            	 	->where('id','=',$parentId)->first();
					 if ($poll) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=',$poll->parent_type)
	                ->where('parent','=',$poll->parent)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["moderator","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	}
            }
            if ($parentType == 'projects') {
            	 $project = \DB::table('projects')
            	 	->where('id','=',$parentId)->first();
					 if ($project) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=','projects')
	                ->where('parent','=',$project->id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["moderator","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	}
            }

            if ($parentType == 'tasks') {
					 $project = false;            	 
            	 $task = \DB::table('tasks')
            	 	->where('id','=',$parentId)->first();
            	 if ($task) {	
            	 	$project = \DB::table('projects')
            	 		->where('id','=',$task->project_id)->first();
            	 }		
					 if ($project) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=','projects')
	                ->where('parent','=',$project->id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["moderator","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	}
            }
            
            if ($parentType == 'products') {
            	 $product = \DB::table('products')
            	 	->where('id','=',$parentId)->first();
					 if ($product) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=','teams')
	                ->where('parent','=',$product->team_id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["moderator","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	}
            }

            if ($parentType == 'events') {
                $event = \DB::table('products')
                ->where('id','=',$parentId)->first();
                if ($event) {
                    $member = \DB::table('members')
                    ->where('parent_type','=',$event->parent_type)
                    ->where('parent','=',$event->parent)
                    ->where('user_id','=', \Auth::user()->id)
                    ->whereIn('rank',["moderator","admin"])
                    ->where('status','=','active')
                    ->orderBy('rank','asc')
                    ->first();
                    if ($member) {
                        $result = true;
                    }
                }
            }
            // first user a system admin, ő is moderátor
            $firstUser = \DB::table('users')->orderBy('id')->first();
            if ($firstUser) {
                if ($firstUser->id == \Auth::user()->id) {
                    $result = true;
                }
            }
        }
        return $result;
    }
    
    public static function isMember(string $parentType, int $parentId): bool {
        $result = false;
        if (\Auth::user()) {
            if ($parentType == 'teams') {
                $member = \DB::table('members')
                ->where('parent','=',$parentId)
                ->where('parent_type','=',$parentType)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',["member","admin"])
                ->where('status','=','active')
                ->orderBy('rank','asc')
                ->first();
                if ($member) {
                    $result = true;
                }
            }
            if ($parentType == 'polls') {
            	 $poll = \DB::table('polls')
            	 	->where('id','=',$parentId)->first();
            	 if ($poll) {
	                $member = \DB::table('members')
	                ->where('parent_type','=',$poll->parent_type)
	                ->where('parent','=',$poll->parent)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["member","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	 }
            }
            if ($parentType == 'projects') {
            	 $project = \DB::table('projects')
            	 	->where('id','=',$parentId)->first();
            	 if ($project) {
	                $member = \DB::table('members')
	                ->where('parent_type','=','projects')
	                ->where('parent','=',$project->id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["member","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	 }
            }
            if ($parentType == 'tasks') {
					 $project = false;            	 
            	 $task = \DB::table('tasks')
            	 	->where('id','=',$parentId)->first();
            	 if ($task) {	
            	 	$project = \DB::table('projects')
            	 		->where('id','=',$task->project_id)->first();
            	 }		
					 if ($project) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=','projects')
	                ->where('parent','=',$project->id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["member","admin"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	}
            }
            if ($parentType == 'products') {
                $product = \DB::table('products')
            	 	->where('id','=',$parentId)->first();
            	 // team member?	
					 if ($product) {            	
	                $member = \DB::table('members')
	                ->where('parent_type','=','teams')
	                ->where('parent','=',$product->team_id)
	                ->where('user_id','=', \Auth::user()->id)
	                ->whereIn('rank',["moderator","admin","member"])
	                ->where('status','=','active')
	                ->orderBy('rank','asc')
	                ->first();
	                if ($member) {
	                    $result = true;
	                }
             	 }
             	 // felhasználó?
             	 $customer = \DB::table('orderitems')
             	 	->leftJoin('orders','orders.id','orderitems.order_id')
             	 	->where('orderitems.product_id','=',$product->id)
             	 	->where('orderitems.status','=','success')
             	 	->where('orders.user_id','=',\Auth::user()->id)
             	 	->first();
             	 if ($customer) {
							$result = true;             	 
             	 }	
            }
            if ($parentType == 'files') {
                $result =  \App\Models\Member::userAdmin($parentType, $parentId);
            }
            if ($parentType == 'events') {
                $result =  true; // eseményhez mindenki hozzá szólhat
            }
        }
        return $result;
    }

    /**
     * olvasott jelzés beállítása
     * @param int $messageId
     * @param int $userId
     */
    public static function setReaded(int $messageId, int $userId) {
        if ($messageId <= 0) {
            return;
        }
        $table = \DB::table('msgreads');
        $rec = $table->where('msg_id','=',$messageId)
                     ->where('user_id','=',$userId)
                     ->first();
        if (!$rec) {
            $table->insert([
                "msg_id" => $messageId,
                "user_id" => $userId
            ]);
        }
    }
    
	/**
	 * parent olvasása
	 * @param string $parentType
	 * @param int $parentId
	 * @return object|false
	 */ 
    public static function getParent($parentType, $parentId) {
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parentId)->first();
        if (!$parent) {
            echo 'Fatal error parent not found'; exit();
        }
        return $parent;
    }    

		
	/**
	 * ReplyTo path olvasása
	 * @param int $replyTo
	 * @return array
	 */ 
	public static function getReplyPath(int $replyTo):array {	
		$path = [];
		$rec = \DB::table('messages')->where('id','=', $replyTo)->first();
		if ($rec) {
			$path[] = $rec;
			while ($rec->reply_to > 0) {
				$rec = \DB::table('messages')->where('id','=', $rec->reply_to)->first();
				
				if ($rec) {
					$path[] = $rec;
				} else {
					$rec = new \stdClass();
					$rec->replyTo = 0;
				}
			}
		   $path = array_reverse($path);
		}
		return $path;
	}
	
	/**
	 * Új message tárolása, módosítás tárolása vagy moderálás tárolása
	 * @param Request
	 * @param bool $member
	 * @param boll $moderator
	 * @return string
	 */ 
	public function createOrUpdate(Request $request, bool $member, bool $moderator):string {
		$errorInfo = '';
        if (\Auth::user()) {
            $userId = \Auth::user()->id;
            $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
        } else {
            $userId = 0;
            $avatar = '';
        }
        $parent_type = $request->input('parent_type');
        $parent = $request->input('parent');
        $msg_type = $request->input('msg_type');
        $reply_to = $request->input('reply_to');
        $value = $request->input('value');
        $messageId = $request->input('messageId',0);
        $backURL = $request->input('backURL','');
        $old = $this->where('id','=',$messageId)->first();
        if ($messageId == 0) {
            // new message
            $moderatorInfo = '';
            $moderatorId = 0;
            if (($userId > 0) & ($member)) {
                // $model = new \App\Models\Message();
                try {
                    $newMessageId = \DB::table('messages')->insertGetId([
                        'parent_type' => $parent_type,
                        'parent' => $parent,
                        'msg_type' => '',
                        'reply_to' => $reply_to,
                        'value' => $value,
                        'user_id' => $userId,
                        'moderator_info' => $moderatorInfo,
                        'moderated_by' => $moderatorId
                    ]);
                } catch (\Illuminate\Database\QueryException $exception) {
                    $errorInfo = JSON_encode($exception->errorInfo);
                }
            } else {
                $errorInfo = 'not logged or not member';
            }
        } else if (($moderator) | (\Auth::user()->id == $old->user_id)) {
            // moderálás vagy módosítás tárolása    
            if ($moderator) {
                $moderatorId = $userId;
                $moderatorInfo = $request->input('moderator_info','');
            } else {
                $moderatorId = $old->moderated_by;
                $moderatorInfo = $old->moderator_info;
            }    
            if (($moderatorInfo == null) | ($moderatorInfo == '')) {
                $moderatorInfo = $old->moderator_info;
            }
            $log = \App\Models\Minimarkdown::buildLog($old->value, $value);
            if ($log != '') {
                $value .= '{log}'.$log;
            }
            try {
                \DB::table('messages')
                ->where('id','=',$messageId)
                ->update([
                    'value' => $value,
                    'moderator_info' => $moderatorInfo,
                    'moderated_by' => $moderatorId
                ]);
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = JSON_encode($exception->errorInfo);
            }
        } else {
            $errorInfo = __('messages.accesDenied');
        }
		return $errorInfo;
	}

	/**
	 * moderátorok beolvasása
	 * @param string $parentType
	 * @param int $parentId
	 * @return array
	 */ 
	public static function getModerators(string $parentType, int $parentId) {
        return \DB::table('members')
                ->where('parent_type','=',$parentType)
                ->where('parent','=',$parentId)
                ->whereIn('rank',['admin','moderator'])
                ->get();
    }      
    
    /**
     * Bejelentkezett user olvasatlan üzenetei query builder
     * @param int $pageSize
     */
    public static function getNotreadedQuery() {
        $user = \Auth::user();
        if ($user) {
             
             $msgTeams = \DB::table('messages')
             ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                 'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                 'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                 'teams.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('teams','teams.id','messages.parent')
                 ->join('members','members.parent','teams.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','teams')
                 ->where('members.parent_type','=','teams')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
                 
                 $msgProjects = \DB::table('messages')
                 ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                     'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                     'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                     'projects.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('projects','projects.id','messages.parent')
                 ->join('members','members.parent','projects.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','projects')
                 ->where('members.parent_type','=','projects')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
                     
                  $msgProductsTeams = \DB::table('messages')
                  ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                         'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                         'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                         'products.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('products','products.id','messages.parent')
                 ->join('teams','teams.id','products.parent')
                 ->join('members','members.parent','teams.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','products')
                 ->where('products.parent_type','=','teams')
                 ->where('members.parent_type','=','teams')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
                         
                 $msgProductsUsers = \DB::table('messages')
                 ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                     'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                     'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                     'products.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('products','products.id','messages.parent')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','products')
                 ->where('products.parent_type','=','users')
                 ->where('products.parent','=',$user->id)
                 ->whereNull('msgreads.id');

                 $msgPollsTeams = \DB::table('messages')
                 ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                     'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                     'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                     'polls.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('polls','polls.id','messages.parent')
                 ->join('teams','teams.id','polls.parent')
                 ->join('members','members.parent','teams.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','polls')
                 ->where('polls.parent_type','=','teams')
                 ->where('members.parent_type','=','teams')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
                 
                 $msgPollsTeams = \DB::table('messages')
                 ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                     'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                     'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                     'polls.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('polls','polls.id','messages.parent')
                 ->join('teams','teams.id','polls.parent')
                 ->join('members','members.parent','teams.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','polls')
                 ->where('polls.parent_type','=','teams')
                 ->where('members.parent_type','=','teams')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
                     
                 $msgPollsProjects = \DB::table('messages')
                 ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                         'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                         'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                         'polls.name as pname')
                 ->join('users as users1','users1.id','messages.user_id')
                 ->leftJoin('users as users2','users2.id','messages.reply_to')
                 ->join('polls','polls.id','messages.parent')
                 ->join('projects','projects.id','polls.parent')
                 ->join('members','members.parent','projects.id')
                 ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
                 ->where('messages.parent_type','=','polls')
                 ->where('polls.parent_type','=','projects')
                 ->where('members.parent_type','=','projects')
                 ->where('members.user_id','=',$user->id)
                 ->where('members.status','=','active')
                 ->whereNull('msgreads.id');
             
             return \DB::table('messages')
             ->select('users1.id as cid','users1.profile_photo_path as cavatar','users1.name as cname','users1.email as cemail',
                 'users2.id as rid','users2.profile_photo_path as ravatar','users2.name as rname',
                 'messages.value','messages.parent_type','messages.parent', 'messages.created_at',
                 'users3.name as pname')
             ->join('users as users1','users1.id','messages.user_id')
             ->leftJoin('users as users2','users2.id','messages.reply_to')
             ->join('users as users3','users3.id','messages.parent')
             ->leftJoin('msgreads', function($join) {
                     $join->on('msgreads.msg_id','=','messages.id')
                     ->where('msgreads.user_id','=',\Auth::user()->id);
                 })
             ->where('messages.parent_type','=','users')
             ->where('messages.parent','=',$user->id)
             ->whereNull('msgreads.id')
             ->union($msgTeams)
             ->union($msgProjects)
             ->union($msgProductsTeams)
             ->union($msgProductsUsers)
             ->union($msgPollsTeams)
             ->union($msgPollsProjects);
        } else {
            echo 'Fatal error not logged'; exit();
        }
    }
    
    /**
     * Olvasatlan üzenetek lapozható lekérdezése
     * @param int $pageSize
     * @return paginator object
     */
    public static function getNotreaded( $pageSize) {
        $q = Message::getNotReadedQuery();
        return $q->orderBy('created_at')->paginate($pageSize);
    }
    
    /**
     * Olvasatlan üzenetek száma
     * @param int $pageSize
     * @return int
     */
    public static function getNotreadedCount() {
        $q = Message::getNotReadedQuery();
        return $q->count();
    }
    
                

}
