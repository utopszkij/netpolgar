<?php

class LikeRecord {
   public $type = '';
   public $id = 0;
   public $user_id = 0; 
   public $user_member = 0; // bool
   public $like_type = 'like'; // 'dislike'
}

class LikeModel {
    
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
        
    protected function obj2LikeRecord($obj): LikeRecord {
        $result = new LikeRecord();
        foreach ($result as $fn => $fv) {
            $result->$fn = $obj->$fn;
        }
        return $result;
    }

    /**
     * group like/dislike automatikus akció végrehajtása
     * @param string $type
     * @param int $id
     * @param object $counts {upTotal, downTotal, upMember, downMember}
     */
    public function groupAutoActions(string $type, int $id, $result) {
        try {
            if (!$this->groupModel) {
                include_once './models/groups.php';
                $this->groupModel = new GroupsModel();
            }
            if (!$this->memberModel) {
                include_once './models/members.php';
                $this->memberModel = new MembersModel();
            }
            $group = $this->groupModel->getRecord($id);
            if ($group->id > 0) {
                    // $options = JSON_decode(str_replace("\n",'',$group->options));
                    if (($group->state == 'proposal') & ($group->group_to_active > 0)) {
                        if ($result->upTotal >= ($result->downTotal + $group->group_to_active)) {
                            $group->state = 'active';
                            $this->groupModel->save($group, new UserRecord());
                        }
                    }
                    if (($group->state == 'active') & ($group->group_to_close > 0)) {
                         $memberCount = $this->memberModel->getMemberCount('group', $id);
                        if ($result->downMember >= ($memberCount * $group->group_to_close / 100)) {
                            $group->state = 'closed';
                            $this->groupModel->save($group, new UserRecord());
                        }
                    }
            } // megvan a group rekord
        } finally {
            ;
        }
    }
    
    /**
     * like/dislike automatikus akciók végrehajtása
     * @param string $type
     * @param int $id
     * @param object $counts {upTotal, downTotal, upMember, downMember}
     */
    public function autoActions(string $type, int $id, $result) {
       if ($type == 'group') {
            $this->groupAutoActions($type, $id, $result);
       }
    }
    
    
    public function getCounts(string $type, int $id) {
        $result = new stdClass();

        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['like_type','=','like']);
        $result->upTotal = ''.$table->count();
        
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['like_type','=','dislike']);
        $result->downTotal = ''.$table->count();
        
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['like_type','=','like']);
        $table->where(['user_member','=',1]);
        $result->upMember = ''.$table->count();
        
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['like_type','=','dislike']);
        $table->where(['user_member','=',1]);
        $result->downMember = ''.$table->count();
        $this->autoActions($type, $id, $result);
        return $result;
    }
    
    public function check(string $type, int $id, int $user_id): string {
        $result = '';
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$user_id]);
        $rec = $table->first();
        if ($rec) {
            $result = $rec->like_type;
        }
        return $result;
    }
    
    public function set(string $type, int $id, string $like_type, int $user_id) {
        $table = new Table('likes');
        $data = new LikeRecord();
        $data->type = $type;
        $data->id = $id;
        $data->user_id = $user_id;
        $data->user_member = 0;
        $data->like_type= $like_type;
        
        // a tipustól függően meg kell állapítani, hogy a user member vagy nem...
        if ($type == 'group') {
            if (!$this->memberModel) {
                include_once './models/members.php';
                $this->memberModel = new MembersModel();
            }
            if ($this->memberModel->isMember('group', $data->id, $data->user_id)) {
                $data->user_member = 1;
            }
        }
        $table->insert($data);
    }
    
    public function remove(string $type, int $id, string $like_type, int $user_id) {
        $table = new Table('likes');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$user_id]);
        $table->where(['like_type','=',$like_type]);
        $table->delete();
    }
    
    /**
     * get rekordset
     * @param object {type, id, like_type, user_member, offset, limit, orderField, orderDir, filterStr}
     * @return array [{like_type, name, user_member}, ....]
     */
    public function getRecords($p, int &$total): array {
        $db = new DB();
        $whereStr = 'l.like_type="'.$p->like_type.'"';
        if ($p->user_member != 'all') {
            $whereStr .= ' and l.user_member=1';
        }
        $db->setQuery('select count(*) cc
        from `likes` as l
        where l.type="'.$p->type.'" and l.id="'.$p->id.'" and '.$whereStr);        
        $rec = $db->loadObject();
        if ($rec) {
            $total = $rec->cc;
        } else {
            $total = 0;
        }
        
        $db->setQuery('select l.like_type, u.nick, l.user_member
        from `likes` as l, users as u
        where l.type = "'.$p->type.'" and l.id = "'.$p->id.'" and u.id = l.user_id and '.$whereStr.'
        limit '.$p->offset.','.$p->limit);
        $result = $db->LoadObjectList();
        return $result;
    }
    
} // class
?>
