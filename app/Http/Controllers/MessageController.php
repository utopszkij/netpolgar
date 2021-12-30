<?php 
/**
 * üzenetek lik, dislike, voksok kezelése (böngészés, megjelenítés, modositás, felvitel, törlés
 * használja az \Auth::user() -t.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\Models\Message;

/**
 * csoportok kezelő controller osztály
 * @author utopszkij
 */
class MessageController extends Controller {

    protected function isModerator(string $parentType, int $parentId): bool {
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
    
    protected function isMember(string $parentType, int $parentId): bool {
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
        }
        return $result;
    }
    
    protected function avatar($profile_photo_path, $email) {
        if ($profile_photo_path != '') {
            $result = \URL::to('/').$profile_photo_path;
        } else {
            $result = 'https://gravatar.com/avatar/'.md5($email).
            '?d='.\URL::to('/img/noavatar.png');
        }
        return $result;
    }
    
    /**
     * olvasott jelzés beállítása
     * @param int $messageId
     * @param int $userId
     */
    protected function setReaded(int $messageId, int $userId) {
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
     * lapozó linkek kialakítása
     * @param int $offset
     * @param int $total
     * @param string $parentType
     * @param record $parent
     * @return array
     */
    protected function buildLinks(int $offset, int $total, string $parentType, $parent): array {
        $links = [];
        $page = 1;
        $w = 0;
        $links[] = ["first", __('messages.first'), \URL::to('/message/tree/'.$parentType.'/'.$parent->id.'/0') ];
        while ($w < $total) {
            if (($w >= $offset - 20) & ($w <= $offset + 20)) {
                 if ($w == $offset) {
                     $links[] = ["actual", $page, \URL::to('/message/tree/'.$parentType.'/'.$parent->id.'/'.$w) ];
                 } else {
                     $links[] = ["other", $page, \URL::to('/message/tree/'.$parentType.'/'.$parent->id.'/'.$w) ];
                 }
             }
            $w = $w + 10;
            $page++;
        }
        $page = $page - 1;
        $w = $w - 10;
        $links[] = ["last", __('messages.last'), \URL::to('/message/tree/'.$parentType.'/'.$parent->id.'/'.$w) ];
        return $links;
    }
    
    /**
     * üzenetek fa szerkezetű lapozható megjelenitője
     */
    public function tree(Request $request, string $parentType, int $parent, int $offset = -1) {
        if (\Auth::user()) {
            $userId = \Auth::user()->id;
            $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
        } else {
            $userId = 0;
            $avatar = '';
        }
        
        // privát üzenetek közül csak a saját üzeneteit olvashatja
        if (($parentType == 'users') & ($parent != $userId)) {
            return \Redirect::back()->with('error',__('messages.accessDenied'));
        }
        
        $moderator = $this->isModerator($parentType, $parent);
        $member = $this->isMember($parentType, $parent);
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'Fatal error parent not found'; exit();
        }
        $model = new \App\Models\Message();
        $model->getTreeItem($parentType, $parent->id, 0, 0);
        $model->setPathNotReaded();
        $model->treeTruncate();
        $total = count($model->tree);
        
        // ha $offset alapértelmezett akkor az utolsó lapot jelenitem meg
        if ($offset < 0) {
            $offset = 0;
            while ($offset < (count($model->tree) - 10)) {
                $offset = $offset + 10;
            }
        }
        
        $model->tree = array_splice($model->tree, $offset, 10);
        foreach ($model->tree as $treeItem) {
            $model->getInfo($treeItem);
            $this->setReaded($treeItem->id, $userId);
        }
        
        $path = [];
        if ($total > 0)  {
            $rec = $model->tree[0];
            while ($rec->replyTo[0] > 0) {
                $rec = \DB::table('messages')->where('id','=',$rec->replyTo[0])->first();
                if ($rec) {
                    $model->getInfo($rec);
                    $path[] = $rec;
                } else {
                    $rec = new \stdClass();
                    $rec->replyTo = [0,''];
                }
            }
            $path = array_reverse($path);
            $level = 0;
            foreach ($path as $pathItem) {
                $pathItem->level = $level;
                $level++;
            }
        }
        
        $links = $this->buildLinks($offset, $total, $parentType, $parent);
        
        return view('message.tree',[
           "avatar" => $avatar, 
           "parentType" => $parentType,
           "parentId" => $parent->id,
           "parent" => $parent,  
           "member" => $member,
           "moderator" => $moderator,
           "path" => $path,
           "tree" => $model->tree,
           "total" => $total,
           "links" => $links
        ]);        
    }
    
    /**
     * üzenetek lista formájú lapozható megjelenitője
     */
    public function list(Request $request, string $parentType, int $parent, int $replyTo, int $offset = -1) {
        if (\Auth::user()) {
            $userId = \Auth::user()->id;
            $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
        } else {
            $userId = 0;
            $avatar = '';
        }
        
        // privát üzenetek közül csak a saját üzeneteit olvashatja
        if (($parentType == 'users') & ($parent != $userId)) {
            return \Redirect::back()->with('error',__('messages.accessDenied'));
        }
        
        $moderator = $this->isModerator($parentType, $parent);
        $member = $this->isMember($parentType, $parent);
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'Fatal error parent not found'; exit();
        }
        
        // Ha itt olvasnám be a path -ot, akkor a model->getListItem-nek tudnám a kezdő level értéket adni
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
        $model = new \App\Models\Message();
        $level = 0;
        foreach ($path as $pathItem) {
            $pathItem->level = $level;
            $model->getInfo($pathItem);
            $level++;
        }
        $model->getListItem($parentType, $parent->id, $replyTo, $level);
        $total = count($model->tree);
        
        // ha $offset alapértelmezett akkor az utolsó lapot jelenitem meg
        // ha $offset alapértelmezett akkor az utolsó lapot jelenitem meg
        if ($offset < 0) {
            $offset = 0;
            while ($offset < count($model->tree)) {
                $offset = $offset + 10;
            }
        }
        
        $model->tree = array_splice($model->tree, $offset, 10);
        foreach ($model->tree as $treeItem) {
            $model->getInfo($treeItem);
            $this->setReaded($treeItem->id, $userId);
        }
                
        $links = $this->buildLinks($offset, $total, $parentType, $parent);
        
        return view('message.tree',[
            "avatar" => $avatar,
            "parentType" => $parentType,
            "parentId" => $parent->id,
            "parent" => $parent,
            "member" => $member,
            "moderator" => $moderator,
            "path" => $path,
            "tree" => $model->tree,
            "total" => $total,
            "links" => $links
        ]);
    }
    
    
    /**
     * új üzenet tárolása és olvasottság jelzés,
     * moderálás tárolása
     * csak bejelentkezett csoport member használhatja
     * @param Request $request
     * @return laravel redirect
     */
    public function store(Request $request) {
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
        
        $moderator = $this->isModerator($parent_type, $parent);
        if ($parent_type == 'users') {
            $member = true;
        } else {
            $member = $this->isMember($parent_type, $parent);
        }
        
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
                    $result = \Redirect::back()->with('success',__('messages.saved'));
                } catch (\Illuminate\Database\QueryException $exception) {
                    $errorInfo = $exception->errorInfo;
                    $result = \Redirect::back()->with('error',JSON_encode($errorInfo));
                }
            } else {
                $result = \Redirect::back()->with('error',__('messages.accesDenied'));
            }
        } else if ($moderator) {
            // moderálás tárolása    
            $moderatorInfo = $request->input('moderator_info','');
            $moderatorId = $userId;
            try {
                \DB::table('messages')
                ->where('id','=',$messageId)
                ->update([
                    'value' => $value,
                    'moderator_info' => $moderatorInfo,
                    'moderated_by' => $moderatorId
                ]);
                $result = \Redirect::to( urldecode($backURL));
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = $exception->errorInfo;
                $result = \Redirect::back()->with('error',JSON_encode($errorInfo));
            }
        } else {
            $result = \Redirect::back()->with('error',__('messages.accesDenied'));
        }
        return $result;
    }
    
    /**
     * moderátor képernyő megjelenítése
     * @param string $messageId
     * @return laravel view
     */
    public function moderal(string $messageId) {
        $model = new \App\Models\Message();
        $message = $model->where('id','=', $messageId)->first();
        if ($message) {
            if (\Auth::user()) {
                $userId = \Auth::user()->id;
                $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
            } else {
                $userId = 0;
                $avatar = '';
            }
            if ($this->isModerator($message->parent_type, $message->parent)) {
                $result = view('message.moderator',[
                    "myMessage" => $message,
                    "backURL" => urlencode(\URL::previous())
                ]);
            } else {
                $result = \Redirect::back()->with('error',__('messages.accesDenied'));
            }
            
        } else {
            echo 'Fatal error message not found'; exit();
        }
        return $result;
    }
    
    /**
     * üzenet kifogásolás
     * @param string $messageId
     */
    public function protest(string $messageId) {
        $message = \DB::table('messages')->where('id','=',$messageId)->first();
        if ($message) {
            $moderators = \DB::table('members')
                ->where('parent_type','=',$message->parent_type)
                ->where('parent','=',$message->parent)
                ->whereIn('rank',['admin','moderator'])
                ->get();
                if (count($moderators) > 0) {
                    return view('message.protest',[
                        'myMessage' => $message,
                        'moderators' => $moderators
                    ]);
                } else {
                    return \Redirect::back()->with('error',__('messages.notModerator'));
                }
        } else {
            echo 'fatal error message not found'; exit();
        }
    }
    
    /**
     * üzenet kifogásolás tárolása; privát üzenet küldése a moderátoroknak
     * @param Request $request
     * @return unknown
     */
    public function saveprotest(Request $request) {
        if (\Auth::user()) {
            $userId = \Auth::user()->id;
        } else {
            $userId = 0;
        }
        $moderators = explode(',',$request->input('moderators'));
        $messageId = $request->input('messageId');
        $txt = $request->input('txt');
        foreach ($moderators as $moderator) {
            $newMessageId = \DB::table('messages')->insertGetId([
                'parent_type' => 'users',
                'parent' => $moderator,
                'msg_type' => '',
                'reply_to' => 0,
                'value' => $txt."\n".\URL::to('/message/moderal/'.$messageId),
                'user_id' => $userId,
                'moderator_info' => '',
                'moderated_by' => 0
            ]);
            
        }
        return \Redirect::to(\URL::to('/'))->with('success',__('messages.protestSended'));
    }
    
}

