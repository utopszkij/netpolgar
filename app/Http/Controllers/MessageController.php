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

    
    protected function avatar($profile_photo_path, $email) {
        return \App\Models\Avatar::userAvatar($profile_photo_path, $email);
    }
    
    /**
     * lapozó linkek kialakítása
     * @param int $offset
     * @param int $total
     * @param string $parentType
     * @param record $parent
     * @return array
     */
    protected function buildLinksTree(int $offset, int $total, string $parentType, $parent): array {
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
     * lapozó linkek kialakítása
     * @param int $offset
     * @param int $total
     * @param string $parentType
     * @param record $parent
     * @return array
     */
    protected function buildLinksList(int $offset, int $total, string $parentType, $parent): array {
        $links = [];
        $page = 1;
        $w = 0;
        $links[] = ["first", __('messages.first'), \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/0') ];
        while ($w < $total) {
            if (($w >= $offset - 20) & ($w <= $offset + 20)) {
                 if ($w == $offset) {
                     $links[] = ["actual", $page, \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$w) ];
                 } else {
                     $links[] = ["other", $page, \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$w) ];
                 }
             }
            $w = $w + 10;
            $page++;
        }
        $page = $page - 1;
        $w = $w - 10;
        $links[] = ["last", __('messages.last'), \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$w) ];
        for ($i=0; $i<count($links); $i++) {
            $links[$i][2] .= '?filterUserName='.\Request::input('filterUserName','');
        }
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
        $moderator = Message::isModerator($parentType, $parent);
        $member = Message::isMember($parentType, $parent);
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'Fatal error parent not found'; exit();
        }
        
        $model = new \App\Models\Message();
        $parent = $model->getParent($parentType, $parent->id);
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
            if ($userId > 0) {
                Message::setReaded($treeItem->id, $userId);
            }
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
        
        $links = $this->buildLinksTree($offset, $total, $parentType, $parent);
        
        return view('message.tree',[
           "title" => __('messages.tree'),
           "avatar" => $avatar, 
           "parentType" => $parentType,
           "parentId" => $parent->id,
           "parent" => $parent,  
           "member" => $member,
           "moderator" => $moderator,
           "path" => $path,
           "tree" => $model->tree,
           "total" => $total,
           "links" => $links,
           "filterUserName" => ""
        ]);        
    }

    /**
     * Üzenet szál megjelenítése, amelyik tartalmazza az $id elemet
     */
    public function thread(Request $request, int $id) {
        $rec = \DB::table('messages')->where('id','=',$id)->first();
        $model = new \App\Models\Message();
        $model->getThread($id);
        if (count($model->tree) == 0) {
            return \Redirect::back()->with('error',__('messages.noThread'));
        }
        $total = count($model->tree);
        if (\Auth::user()) {
            $userId = \Auth::user()->id;
            $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
        } else {
            $userId = 0;
            $avatar = '';
        }
        $parentType = $rec->parent_type;
        $parent = $rec->parent;
        $offset = 0;
        // privát üzenetek közül csak a saját üzeneteit olvashatja
        if (($parentType == 'users') & ($parent != $userId)) {
            return \Redirect::back()->with('error',__('messages.accessDenied'));
        }
        
        $moderator = Message::isModerator($parentType, $parent);
        $member = Message::isMember($parentType, $parent);
        
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'Fatal error parent not found'; exit();
        }
        foreach ($model->tree as $treeItem) {
            $model->getInfo($treeItem);
            if ($userId > 0) {
                Message::setReaded($treeItem->id, $userId);
            }
        }
        $path = [];
        $links = $this->buildLinksList($offset, $total, $parentType, $parent);
        return view('message.tree',[
           "title" => __('messages.thread'), 
           "avatar" => $avatar, 
           "parentType" => $parentType,
           "parentId" => $parent->id,
           "parent" => $parent,  
           "member" => $member,
           "moderator" => $moderator,
           "path" => $path,
           "tree" => $model->tree,
           "total" => $total,
           "links" => $links,
           "filterUserName" => ""
        ]);        

    }
    
    /**
     * üzenetek lista formájú lapozható megjelenitője
     * ha a request -ben filterUserName adott akkor csak az általa írtakat
     * 
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
        $filterUserName = $request->input('filterUserName','');
        
        $moderator = Message::isModerator($parentType, $parent);
        $member = Message::isMember($parentType, $parent);
        $parent = Message::getParent($parentType, $parent);
		$path = Message::getReplyPath($replyTo);
        $model = new \App\Models\Message();
        $level = 0;
        foreach ($path as $pathItem) {
            $pathItem->level = $level;
            $model->getInfo($pathItem);
            $level++;
        }
        if ($filterUserName == '') {
            $model->getListItem($parentType, $parent->id, $replyTo, $level);
        } else {
            $model->tree = $model->getByUser($parentType, $parent->id, $filterUserName);
        }    
        $total = count($model->tree);
        // ha $offset alapértelmezett akkor az utolsó lapot jelenitem meg
        if ($offset < 0) {
            $offset = 0;
            while ($offset < count($model->tree)) {
                $offset = $offset + 10;
            }
            $offset = $offset - 10;
        }
        
        $model->tree = array_splice($model->tree, $offset, 10);
        foreach ($model->tree as $treeItem) {
            $model->getInfo($treeItem);
            if ($userId > 0) {
                Message::setReaded($treeItem->id, $userId);
            }
        }
                
        $links = $this->buildLinksList($offset, $total, $parentType, $parent);
        
        if ($filterUserName != '') {
            $title = $filterUserName.' '.__('messages.ofMessages');
        } else {
            $title = __('messages.list');
        }
        return view('message.tree',[
            "title" => $title,
            "avatar" => $avatar,
            "parentType" => $parentType,
            "parentId" => $parent->id,
            "parent" => $parent,
            "filterUserName" => $filterUserName,
            "member" => $member,
            "moderator" => $moderator,
            "path" => $path,
            "tree" => $model->tree,
            "total" => $total,
            "links" => $links
        ]);
    }
    
    
    /**
     * új üzenet tárolása
     * moderálás vagy modosítás tárolása
     * csak bejelentkezett csoport member használhatja
     * @param Request $request
     * @return laravel redirect
     */
    public function store(Request $request) {
		$model = new Message();
        $parent_type = $request->input('parent_type');
        $parent = $request->input('parent');
        $moderator = $model->isModerator($parent_type, $parent);
        if ($parent_type == 'users') {
            $member = true;
        } else {
            $member = Message::isMember($parent_type, $parent);
        }
        $errorInfo = $model->createOrUpdate($request, $member, $moderator);
        if ($parent_type == 'users') {
			if ($errorInfo == '') {
				$result = redirect(\URL::to('/message/tree/users/'.\Auth::user()->id))
				->with('success',__('messages.saved'));
			} else {
				$result = \Redirect::back()->with('error',$errorInfo);
			}
		} else {
            $url = \URL::to('/message/tree/'.$parent_type.'/'.$parent);
			if ($errorInfo == '') {
				$result = \Redirect::to($url)->with('success',__('messages.saved'));
			} else {
				$result = \Redirect::to($url)->with('error',$errorInfo);
			}
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
        $message = Message::getRecord($messageId);
        if ($message) {
            if (\Auth::user()) {
                $userId = \Auth::user()->id;
                $avatar = $this->avatar(\Auth::user()->profile_photo_path, \Auth::user()->email);
            } else {
                $userId = 0;
                $avatar = '';
            }
            if (Message::isModerator($message->parent_type, $message->parent)) {
                $result = view('message.moderator',[
                    "myMessage" => $message,
                    "backURL" => urlencode(\URL::previous())
                ]);
            } else if (\Auth::check() & (\Auth::user()->id == $message->user_id)) {
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
        $message = Message::where('id','=',$messageId)->first();
        if ($message) {
			$moderators = Message::getModerators($message->parent_type, $message->parent);
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
    
    public function notreaded() {
        if (\Auth::check()) {
            $data = Message::getNotreaded(8);
            return view('message.notreaded',['data' => $data])
            ->with('i', (request()->input('page', 1) - 1) * 8);
        } else {
            echo 'fatal error not logged'; exit();
        }
    }
    
}

