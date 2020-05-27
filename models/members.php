<?php

class MemberRecord {
    public $type;    // objektum tipus ('group',....)
    public $id;      // objektum id
    public $user_id; // user id
    public $state;   // 'proposal', 'invited', 'candydate', 'active', 'pause', 'exiting', 'exited'
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

    
    public function isMember(string $type, int $id, int $user_id): bool {
        return false;
    }
    
    public function isUserAdmin(string $type, int $id, int $user_id): bool {
        return false;
    }
    
    
    public function getMemberCount(string $type, int $id): int {
        return 0;
    }
    
    public function addMember(string $type, int $id, int $user_id, string $state): array {
        return [];
    }
    
    public function deleteMember(string $type, int $id, int $user_id): array {
        return [];
    }
    
    public function setMemberState(string $type, int $id, int $userId, string $state): array {
        return [];
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
