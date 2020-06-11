<?php

class MemberRecord {
    public $id = 0;         // record ID
    public $type = '';      // objektum tipus ('group',....)
    public $object_id = 0;  // objektum id
    public $user_id = 0;    // user id
    public $state = '';     // 'aspirant', 'active', 'pause', 'excluded', 'exited', 'admin'
}

class MembersModel {
    function __construct() {
        $db = new DB();
        $db->createTable('members',
            [['id','INT',11,true],
             ['type','VARCHAR',32,false],
             ['object_id','INT',11,false],
             ['user_id','INT',11,false],
             ['state','VARCHAR',32,false]
            ],
            ['id','object_id','user_id']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create members table '.$db->getErrorMsg(); exit();
        }
    }

    /**
     * get active+admin members count
     * @param string $type
     * @param int $objectId
     * @return int
     */
    public function getMemberCount(string $type, int $objectId): int {
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['object_id','=',$objectId]);
        $table->where(['state','=','active']);
        $c1 = $table->count();
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['object_id','=',$objectId]);
        $table->where(['state','=','admin']);
        return $c1 + $table->count();
    }
    
    /**
     * get members filter by states
     * @param string $type
     * @param int $objectId
     * @param array $states
     * @return array of {userId, likeCount, disLikeCount}
     */
    public function getMembersLike(string $type, int $objectId, string $state): array {
        $result = [];
        $filter = new Filter('members','m');
        $filter->setColumns('m.user_id userId, count(l1.*) likeCount, count(l2.*) dislikeCount');
        $filter->join('left outer join','likes','l1','l1.type = m.type and l1.id = m.id and l1.like_type="like"');
        $filter->join('left outer join','likes','l2','l2.type = m.type and l2.id = m.id and l2.like_type="dislike"');
        $filter->group(['m.user_id']);
        $filter->where(['m.type','=',$type]);
        $filter->where(['m.id','=',$objectId]);
        $filter->where(['m.state','=',$state]);
        $result = $filter->get();
        return $result;
    }
    
    /**
     * set member state
     * @param string $type
     * @param int $objectId
     * @param int $userId
     * @param string $state
     */
    public function setMemberState(string $type, int $objectId, int $userId, string $state) {
        $this->updateMember($type, $objectId, $userId, $state);
            
    }
    
    /**
     * get user state in type object
     * @param string $type
     * @param int $id
     * @param int $user_id
     * @return string  // member state or 'none'
     */
    public function getState(string $type, int $object_id, int $user_id): string {
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['object_id','=',$object_id]);
        $table->where(['user_id','=',$user_id]);
        $rec =$table->first();
        if ($rec) {
            $result = $rec->state;
        } else {
            $result = 'none';
        }
        return $result;
    }
    
    public function addMember(string $type, int $object_id, int $user_id, string $state): array {
        $msgs= [];
        $table = new Table('members');
        $memberRec = new MemberRecord();
        $memberRec->type = $type;
        $memberRec->object_id = $object_id;
        $memberRec->user_id = $user_id;
        $memberRec->state = $state;
        $table->insert($memberRec);
        if ($table->getErrorMsg() != '') {
            $msgs[] = $table->getErrorMsg();
        }
        return $msgs;
    }
    
    public function deleteMember(string $type, int $object_id, int $user_id): array {
        $result = [];
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['object_id','=',$object_id]);
        $table->where(['user_id','=',$user_id]);
        $table->delete();
        if ($table->getErrorMsg() != '') {
            $result[] = $table->getErrorMsg();
        }
        return $result;
    }
    
    public function updateMember(string $type, int $object_id, int $userId, string $state): array {
        $msgs= [];
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['object_id','=',$object_id]);
        $table->where(['user_id','=',$userId]);
        $memberRec = new MemberRecord();
        $memberRec->type = $type;
        $memberRec->object_id = $object_id;
        $memberRec->user_id = $userId;
        $memberRec->state = $state;
        $table->update($memberRec);
        if ($table->getErrorMsg() != '') {
            $msgs[] = $table->getErrorMsg();
        }
        return $msgs;
    }
    
    /**
     * get rekord set
     * @param object $p - type, objectId, offset, limit, oderField, orderDir, searchStr, filterState
     * @param int $total
     * @return array
     */
    public function getRecords($p, int &$total): array {
        $total = 0;
        $filter = new Filter('members','m');
        $filter->setColumns('m.id, m.user_id, u.avatar, u.nick,  m.state');
        $filter->join('left outer join','users','u','u.id = m.user_id');
        $filter->where(['m.type','=',$p->type]);
        $filter->where(['m.object_id','=',$p->objectId]);
        if ($p->searchstr != '') {
            $filter->where(['u.nick','like','%'.$p->searchstr.'%']);
        }
        if ($p->filterState != '') {
            $filter->where(['m.state','=',$p->filterState]);
        }
        $filter->order($p->order.' '.$p->order_dir);
        $filter->offset($p->offset);
        $filter->limit($p->limit);
        $total = $filter->count();
        return $filter->get();
    }
    
    /**
     * get member record by id
     * @param int $id
     * @return MemberRecord
     */
    public function getRecord(int $id): MemberRecord {
        $result = new MemberRecord();
        $table = new Table('members');
        $table->where(['id','=',$id]);
        $res = $table->first();
        if ($res) {
            foreach ($res as $fn => $fv) {
              $result->$fn = $fv;  
            }
        }
        return $result;
    }
    
} // class
?>
