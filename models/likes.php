<?php
/**
 * likes rekord tipus kezelése
 * Fontos: egy user, egy objektumot vagy like -ol, vagy dislike-ol a kettő egszerre nem lehetséges!
 * erről a save -ben gondoskodni kell!
 * @author utopszkij
 */

class LikeRecord {
   public $type = '';
   public $id = 0;
   public $user_id = 0; 
   public $user_member = true; // bool
   public $like_type = 'like'; // 'like' | 'dislike'
}

class LikesModel extends Model {
    
    protected $memberModel = false;
    protected $groupModel = false;
    
    function __construct() {
        $db = new DB();
        $db->createTable('likes',
            [['type','VARCHAR',32,false],
             ['id','INT',11,false],
             ['user_id','INT',11,false],
             ['user_member','INT',1,false],
             ['like_type','VARCHAR',32,false]
            ],
            ['id','user_id']
        );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create likes table '.$db->getErrorMsg(); exit();
        }
        
    } // constructor
    
    /**
     * get like counts
     * @param string $type
     * @param int $objectId
     * @return {up,down, upChecked, downChecked}
     */
    public function getCounts(string $type, int $objectId) {
        global $REQUEST;
        $userId = $REQUEST->sessionGet('loggedUser', new UserRecord())->id;
        if ($userId == '') {
            $userId = 0;
        }
        $result = JSON_decode('{"up":0, "down":0, "upChecked":false, "downChecked":false}');
        $filter = new Filter('likes','l');
        $filter->setColumns('count(*) cc');
        $filter->where(['l.type','=',$type]);
        $filter->where(['l.id','=',$objectId]);
        $filter->where(['l.like_type','=','like']);
        $res = $filter->first();
        if ($res) {
            $result->up = $res->cc;
        }
        
        $filter = new Filter('likes','l');
        $filter->setColumns('count(l.id) cc');
        $filter->where(['l.type','=',$type]);
        $filter->where(['l.id','=',$objectId]);
        $filter->where(['l.like_type','=','dislike']);
        $res = $filter->first();
        if ($res) {
            $result->down = $res->cc;
        }
        
        // loggedUser checked like / unlike ?
        $table = new Table('likes');
        $table->where(['`type`','=',$type]);
        $table->where(['id','=',$objectId]);
        $table->where(['user_id','=',$userId]);
        $res = $table->first();
        if ($res) {
            if ($res->like_type == 'like') {
                $result->upChecked = true;
            }
            if ($res->like_type == 'dislike') {
                $result->downChecked = true;
            }
        }
        
        return $result;
    }
    
    public function saveLike(string $type, int $id, int $userId, string $likeType) {
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$userId]);
        $res = $table->first();
        if ($res) {
            if ($res->like_type == $likeType) {
                $table->delete();
            } else {
                $res->like_type = $likeType;
                $table->update($res);
            }
        } else {
            $res = new LikeRecord();
            $res->type = $type;
            $res->id = $id;
            $res->user_id = $userId;
            $res->like_type = $likeType;
            $table->insert($res);
        }
    }
    
    
} // class
?>
