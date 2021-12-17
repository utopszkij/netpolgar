<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * user avatar url képzése
     * @param string $profile_photo_path
     * @param string $email
     * @return string
     */
    protected function avatar($profile_photo_path, $email): string {
        if ($profile_photo_path != '') {
            $result = URL::to('/').$user->profile_photo_path;
        } else {
            $result = 'https://gravatar.com/avatar/'.md5($email).
            '?d='.\URL::to('/img/noavatar.png');
        }
        return $result;
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
            // var_dump($recs); 
            
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
                $message = \DB::table('messages')->where('id','=',$rec->id)->first();
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
    
    
}
