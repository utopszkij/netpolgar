<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = array('parent_type', 'parent', 'reply_to', 'user_id', 'value', 'msg_type', 'moderated_by', 'moderator_info');
    
    public $tree = [];
    
    function __construct() {
        parent::__construct();
        $this->tree = [];
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

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
     * @param array $tree
     * @param int $parent
     * @param int $replyTo
     * @param int $level
     * @return void
     */
    public function getTreeItem(string $parentType,
        int $parent, int $replyTo, int $level) {
            if (\Auth::user()) {
                $userId = \Auth::user()->id;
            } else {
                $userId = 0;
            }
            $recs = \DB::select('select messages.id, messages.reply_to, msgreads.id as readed
                from messages
                left outer join msgreads
                     on msgreads.msg_id = messages.id and
                        msgreads.user_id = '.$userId.'
                where messages.reply_to = '.$replyTo.' and
                messages.parent_type = "'.$parentType.'" and
                messages.parent = '.$parent.'
                order by messages.id ASC');
            
            // var_dump($recs); 
            
            foreach ($recs as $rec) {
                $i = count($this->tree);
                $this->tree[$i] = new \stdClass();
                $this->tree[$i]->id = $rec->id;
                $this->tree[$i]->replyTo = $rec->reply_to;
                $this->tree[$i]->level = $level;
                $this->tree[$i]->readed = ($rec->readed > 0);
                $this->getTreeItem($parentType, $parent, $rec->id, ($level+1));
            }
    }
    
    /**
     * messages információk kiegészítése 
     */
    public function getInfo() {
        for ($i=0; $i<count($this->tree); $i++) {
            $rec = $this->tree[$i];
            if ($rec->id > 0) {
                $message = \DB::table('messages')->where('id','=',$rec->id)->first();
                if ($message) {
                    $creatorUser = \DB::table('users')
                    ->where('id','=',$message->user_id)
                    ->first();
                    if ($creatorUser) {
                        $this->tree[$i]->creator = $creatorUser->name;
                        $this->tree[$i]->avatar = $this->avatar($creatorUser->profile_photo_path, $creatorUser->email);
                    } else {
                        $this->tree[$i]->creator = '?';
                        $this->tree[$i]->avatar = '?';
                    }
                    $this->tree[$i]->replyTo = [$message->reply_to,''];
                    $replyMessage = \DB::table('messages')
                    ->where('id','=',$message->reply_to)
                    ->first();
                    if ($replyMessage) {
                        $replyUser = \DB::table('users')
                        ->where('id','=',$replyMessage->user_id)
                        ->first();
                        if ($replyUser) {
                            $this->tree[$i]->replyTo[1] = $replyUser->name.':';
                        }
                    }
                    $this->tree[$i]->time = $message->created_at;
                    $this->tree[$i]->replyTo[0] = $message->reply_to;
                    $this->tree[$i]->text = $message->value;
                    $this->tree[$i]->style = $message->msg_type;
                    $this->tree[$i]->moderatorInfo = $message->moderator_info;
                    
                    $this->tree[$i]->likeCount = \DB::table('likes')
                    ->where('parent_type','=','messages')
                    ->where('parent','=',$message->id)
                    ->where('like_type','=','like')
                    ->count();
                    $this->tree[$i]->disLikeCount = \DB::table('likes')
                    ->where('parent_type','=','messages')
                    ->where('parent','=',$message->id)
                    ->where('like_type','=','dislike')
                    ->count();
                    $this->tree[$i]->userLiked = false;
                    $this->tree[$i]->userDisLiked = false;
                    $this->tree[$i]->likeStyle = '';
                    $this->tree[$i]->disLikeStyle = '';
                    $user = \Auth::user();
                    if ($user) {
                        $this->tree[$i]->userLiked = (\DB::table('likes')
                            ->where('parent_type','=','messages')
                            ->where('parent','=',$message->id)
                            ->where('user_id','=',$user->id)
                            ->where('like_type','=','like')
                            ->count() > 0);
                        $this->tree[$i]->userDisLiked = (\DB::table('likes')
                            ->where('parent_type','=','messages')
                            ->where('parent','=',$message->id)
                            ->where('user_id','=',$user->id)
                            ->where('like_type','=','dislike')
                            ->count() > 0);
                    }
                    if ($this->tree[$i]->userLiked) {
                        $this->tree[$i]->likeStyle = 'strong';
                    }
                    if ($this->tree[$i]->userDisLiked) {
                        $this->tree[$i]->disLikeStyle = 'strong';
                    }
                } // message
            } else { // id > 0
                $this->tree[$i]->replyTo = [$this->tree[$i]->replyTo,''];
            }
        } // for
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
     * olvasatlan elemek száma
     * @param array $tree
     * @return int
     */
    protected function notReadedCount() {
        $result = 0;
        for ($i=0; $i < count($this->tree); $i++) {
            if (!$this->tree[$i]->readed) {
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
    protected function delFirstNotReaded() {
        $i = 0;
        $item = false;
        while (($i < count($this->tree)) & (!$item)) {
            if (!$this->tree[$i]->readed) {
                $item = $this->tree[$i];
            } else {
                $i++;
            }
        }
        if ($item) {
            // közvetlen és közvetett gyermekeinek megszámolása
            $j = $i + 1;
            while (($j < count($this->tree)) & ($this->tree[$j]->level > $item->level)) {
                $j++;
            }
            $db = $j - $i - 1; // ennyi gyermeke van
            // közvetlen és közvetett gyermekeinek törlése
            array_splice($this->tree, $i + 1, $db);
            // a $item elem törlése vagy "excluded" jelzés
            if ($i > 0) {
                if ($this->tree[$i-1]->id < 0) {
                    array_splice($this->tree, $i, 1);
                } else {
                    $this->tree[$i]->id = -1; // ez jelzi, hogy "excluded"
                }
            } else {
                $this->tree[$i]->id = -1; // ez jelzi, hogy "excluded"
            }
        }
    }
    
    /**
     * fa méret csökkentése
     */
    public function treeTruncate() {
        while ((count($this->tree) > 100) & ($this->notReadedCount() > 0)) {
            $this->delFirstNotReaded();
        }
    }
    
    
}
