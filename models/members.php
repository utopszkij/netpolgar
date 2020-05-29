<?php

class MemberRecord {
    public $type;    // objektum tipus ('group',....)
    public $id;      // objektum id
    public $user_id; // user id
    public $state;   // 'proposal', 'invited', 'candydate', 'active', 'pause', 'exiting', 'exited', 'admin'
}

class MembersModel {
    function __construct() {
        $db = new DB();
        $db->createTable('members',
            [['type','VARCHAR',32,false],
             ['id','INT',11,false],
             ['user_id','INT',11,false],
             ['state','VARCHAR',32,false]
            ],
            ['id','user_id']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create members table '.$db->getErrorMsg(); exit();
        }
    }

    /**
     * get user state in type object
     * @param string $type
     * @param int $id
     * @param int $user_id
     * @return string // 'proposal', 'invited', 'candydate', 'active', 'pause', 'exiting', 'exited', 'none'
     */
    public function getState(string $type, int $id, int $user_id): string {
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$user_id]);
        $rec =$table->first();
        if ($rec) {
            $result = $rec->state;
        } else {
            $result = 'none';
        }
        return $result;
    }
    
    public function isUserAdmin(string $type, int $id, int $user_id): bool {
        return false;
    }
    
    
    public function getMemberCount(string $type, int $id): int {
        return 0;
    }
    
    public function addMember(string $type, int $id, int $user_id, string $state): array {
        $msgs= [];
        $table = new Table('members');
        $memberRec = new MemberRecord();
        $memberRec->type = $type;
        $memberRec->id = $id;
        $memberRec->user_id = $user_id;
        $memberRec->state = $state;
        $table->insert($memberRec);
        if ($table->getErrorMsg() != '') {
            $msgs[] = $table->getErrorMsg();
        }
        return $msgs;
    }
    
    public function deleteMember(string $type, int $id, int $user_id): array {
        $result = [];
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$user_id]);
        $table->delete();
        if ($table->getErrorMsg() != '') {
            $msgs[] = $table->getErrorMsg();
        }
        return $result;
    }
    
    public function updateMember(string $type, int $id, int $userId, string $state): array {
        $msgs= [];
        $table = new Table('members');
        $table->where(['type','=',$type]);
        $table->where(['id','=',$id]);
        $table->where(['user_id','=',$user_id]);
        $memberRec = new MemberRecord();
        $memberRec->type = $type;
        $memberRec->id = $id;
        $memberRec->user_id = $user_id;
        $memberRec->state = $state;
        $table->update($memberRec);
        if ($table->getErrorMsg() != '') {
            $msgs[] = $table->getErrorMsg();
        }
        return $msgs;
    }

    public function getRecord(string $type, int $id, int $user_id): MemberRecord {
        return new MemberRecord();
    }
    
    /**
     * get rekord set
     * @param object $p - type, id, offset, limit, oderField, orderDir, filterStr
     * @param int $total
     * @return array
     */
    public function getRecords($p, int &$total): array {
        $total = 0;
        return [];
    }
    
} // class
?>
