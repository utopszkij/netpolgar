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
        }
        return $result;
    }
    
    protected function avatar($profile_photo_path, $email) {
        if ($profile_photo_path != '') {
            $result = URL::to('/').$user->profile_photo_path;
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
        $moderator = $this->isModerator($parentType, $parent);
        $member = $this->isMember($parentType, $parent);
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parent)->first();
        
        $model = new \App\Models\Message();
        $model->getTreeItem($parentType, $parent->id, 0, 0);
        $model->setPathNotReaded();
        $model->treeTruncate();
        $total = count($model->tree);
        
        // ha $offset alapértelmezett akkor az utolsó lapot jelenitem meg
        if ($offset < 0) {
            $offset = $total - 10;
            if ($offset < 0 ) {
                $offset = 0;
            }
        }
        $model->tree = array_splice($model->tree, $offset, 10);
        $model->getInfo();
        foreach ($model->tree as $treeItem) {
            $this->setReaded($treeItem->id, $userId);
        }
        
        $path = [];
        if (($total > 0) & ($offset > 0)) {
            $rec = $model->tree[0];
            while ($rec->replyTo[0] > 0) {
                $rec = \DB::table('messages.id, messages.value, users.name')
                ->leftJoin('users','users.id','messages.user_id')
                ->where('messages.id','=',$rec->replayTo[0])->first();
                $path[] = $rec;
            }
            array_reverse($path);
        }
        
        $links = [];
        if ($offset > 0) {
            $links[] = ["first", __('messages.first'), \URL::to('/messages/tree/'.$parentType.'/'.$parent->id.'/0') ];
        }
        if ($offset >= 10) {
            $links[] = ["previos", __('messages.previos'), \URL::to('/messages/tree/'.$parentType.'/'.$parent->id.'/'.($offset-10)) ];
        }
        $links[] = ["actual", (($offset/10) + 1), \URL::to('/messages/tree/'.$parentType.'/'.$parent->id.'/'.($offset-10)) ];
        if ($offset < ($total - 10)) {
            $links[] = ["next", __('messages.next'), \URL::to('/messages/tree/'.$parentType.'/'.$parent->id.'/'.($offset+10)) ];
        }
        if ($offset < ($total - 10)) {
            $links[] = ["last", __('messages.last'), \URL::to('/messages/tree/'.$parentType.'/'.$parent->id.'/'.($total-10)) ];
        }
        
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
     * új üzenet tárolása és olvasottság jelzés
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
        $member = $this->isMember($parent_type, $parent);
        
        
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
                    $this->setReaded($newMessageId, $userId);
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
    
}

