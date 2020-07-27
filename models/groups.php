<?php

class GroupRecord {
   public $id = 0;
   public $name = ''; 
   public $description = ''; 
   public $parent = 0;
   public $state = 'active'; // active, closed, proposal
   public $group_type = '';
   public $reg_mode = 'self'; // admin, invite, self
   public $avatar = ''; // img-re mutató url vagy üres
   public $group_to_active = 5;
   public $group_to_close = 90;
   public $member_to_active = 2;
   public $member_to_exclude = 90;
}

include_once './models/users.php';

class GroupsModel {
    function __construct() {
        $db = new DB();
        $db->createTable('groups',
            [['id','INT',11,true],
             ['name','VARCHAR',128,false],
             ['description','TEXT','',false],
             ['parent','INT',11,false],
             ['state','VARCHAR',32,false],
             ['group_type','VARCHAR',32,false],
             ['reg_mode','VARCHAR',32,false],
             ['avatar','VARCHAR',80,false],
             ['group_to_active','INT',11,false],
             ['group_to_close','INT',11,false],
             ['member_to_active','INT',11,false],
             ['member_to_exclude','INT',11,false]
            ],
            ['id','parent']
        );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create groups table '.$db->getErrorMsg(); exit();
        }
        
    } // constructor
    
    
    /**
     * get subgroups filter by states
     * @param int $groupId
     * @param array $states
     * @return array of {subGroupId, likeCount, disLikeCount}
     */
    public function getSubGroupsLike(int $groupId, string $state): array {
        return [];
    }
    
    /**
     * set subgroup state
     * @param int $groupid
     * @param int $userId
     * @param string $state
     */
    public function setSubGroupState(int $groupId, int $subGroupId, string $state) {
        
    }
    
    /**
     * group rekord tárolás előtti ellenörzése
     * @param GroupRecord $data
     * @return array hibaüzenetek vagy üres tömb
     */
    public function check(GroupRecord &$data): array {
        $msgs = [];
        if ($data->name == '') {
            $msgs[] = 'NAME_REQUED';
        }
        if ($data->description == '') {
            $msgs[] = 'DESCRIPTION_REQUED';
        }
        if (($data->state != 'active') & ($data->state != 'proposal') & ($data->state != 'closed')) {
            $data->state = 'proposal';
        }
        if (($data->reg_mode != 'self') & ($data->reg_mode != 'invite') & 
            ($data->reg_mode != 'canidate') & ($data->reg_mode != 'admin')) {
            $data->reg_mode = 'admin';
        }
        if (!is_numeric($data->group_to_active)) {
            $data->group_to_active = 0;
        }
        if (!is_numeric($data->group_to_close)) {
            $data->group_to_close = 0;
        }
        if (!is_numeric($data->member_to_active)) {
            $data->member_to_active = 0;
        }
        if (!is_numeric($data->member_to_exclude)) {
            $data->member_to_exclude = 0;
        }
        $data->group_to_active = round($data->group_to_active);
        $data->group_to_close = round($data->group_to_close);
        $data->member_to_active = round($data->member_to_active);
        $data->member_to_exclude = round($data->member_to_exclude);
        return $msgs;
    }
    
    /**
     * group rekord törölhető?
     * @param GroupRecord $data
     * @param array $userGroupRights
     * @return array hibaüzenetek vagy üres tömb
     */
    public function canDelete(GroupRecord $data, bool $userGroupAdmin): array {
        $msgs = [];
        if (!$userGroupAdmin) {
            $msgs[] = 'ACCESS_VIOLATION';
        }
        $table = new Table('groups');
        $childs = $table->where(['parent','=',$data->id])->first();
        if ($childs) {
            $table[] = 'CAN_NOT_DELETE_THERE_IS_CHILDS';
        }
        return $msgs;
    }
    
    /**
     * kigyüjti azon groupId -kez ahol ez a user admin
     * @param object $user
     * @return array
     */
    public function getUserGroupRights($user) : array {
      $result = [];
      return $result;
    }
    
    
    /**
     * @param GroupRecord $data
     * @param UserRecord $user - bejelentkezett user
     * @return array hibaüzenetek vagy üres tömb
     */
    public function save(GroupRecord $data, UserRecord $user): array {
        $msgs = [];
        $table = new table('groups');
        if ($data->id == 0) {
            if ($table->insert($data)) {
                $data->id = $table->getInsertedId();
                // members rekord létrehozása
                $table1 = new Table('members');
                $rec = new stdClass();
                $rec->id = 0;
                $rec->type = 'groups';
                $rec->object_id = $data->id;
                $rec->user_id = $user->id;
                $rec->state = 'admin';
                $table1->insert($rec);
            }
        } else {
            unset($data->nick); // nick nem módosítható
            $table->where(['id','=',$data->id]);
            $table->update($data);
        }
        if ($table->getErrorNum() != 0) {
            $msgs[] = $table->getErrorMsg();
        }
        return $msgs;
    }
        
    protected function obj2GroupRecord($obj): GroupRecord {
        $result = new GroupRecord();
        foreach ($result as $fn => $fv) {
            $result->$fn = $obj->$fn;
        }
        return $result;
    }
    
    /**
     * group rekord olvasás id alapján
     * @param int $id
     * @return GroupRecord  id = 0 ha nem található
     */
    public function getRecord(int $id): GroupRecord {
        $table = new Table('groups');
        $rec = $table->where(['id','=',$id])->first();
        if ($rec) {
            $result = $this->obj2GroupRecord($rec);
        } else {
            $result = new GroupRecord();
            $result->id = 0;
        }
        return $result;
    }
    
    
    /**
     * group rekord törlése
     * @param int $id
     * @return array  hibaüzenetek vagy []
     */
    public function delete(GroupRecord $rec): array {
        $result = [];
        $table = new Table('groups');
        $rec = $table->where(['id','=',$rec->id])->first();
        if ($rec) {
            $table->delete();
            if ($table->getErrorNum() > 0) {
                $result[] = $table->getErrorMsg();
            } else {
                // alcsoportok törlése
            }
        } else {
            $result[] = 'NOT_FOUND';
        }
        return $result;
    }
    
    /**
     * A paraméterként kapott itemet és gyermekeit beirja a result -ba
     * filter userId, modositja a total értékét is.
     * @param GroupRecord $item
     * @param array $result [{id, state, avatar, name , childs},...]
     * @param int $total
     * @param int $userId
     */
    public function getItem($item, array & $result, int & $total, int $userId) {
        $total = $total + 1;
        $result[] = new stdClass();
        $itemI = count($result) - 1;
        $result[$itemI]->id = $item->id;
        $result[$itemI]->state = $item->state;
        $result[$itemI]->name = $item->name;
        if ($item->avatar == '') {
            $result[$itemI]->avatar = 'templates/'.config('TEMPLATE').'/no-image.png';
        } else {
            $result[$itemI]->avatar = $item->avatar;
        }
        $this->getSubItems($item->id, $result, $total, $userId);
    }
    
    /**
     * ha a legfelső szinten hivták (total==0) akkor a paraméterként kapott parent gyermek 
     *     rekordjait beirja a result tömbbe,
     * ha nem a legfeéső szinten (total > 0) és vannak gyermek rekordok akkor az 
     *      utolsó elem child értékét modosítja    
     * @param int $parent
     * @param array $result
     * @param int $total
     * @param int $userId
     */
    protected function getSubItems(int $parent, array & $result, int & $total, int $userId) {
        $itemI = count($result) - 1;
        $table = new Table('groups');
        $table->where(['parent','=', $parent]);
        if ($total > 0) {
            $table->limit(1);
        }
        $items = $table->get();
        if (count($items) > 0) {
            if ($total == 0) {
                foreach ($items as $item) {
                    $this->getItem($item, $result, $total, $userId);
                }
            } else {
                $result[$itemI]->childs = [];
            }
        } else {
            if ($itemI >= 0) {
                $result[$itemI]->childs = false;
            }
        }
    }
    
    /**
     * adott group és tulajdosoainak kigyüjtése egészen a Root -id
     * @param int $id
     * @return array of GroupRecord
     */
    public function getGroupPath(int $id): array {
        // get parentPath
        $result = [];
        if ($id > 0) {
            $result[] = $this->getRecord($id);
            while ($result[count($result) - 1]->parent > 0) {
                $result[] = $this->getRecord($result[count($result) - 1]->parent);
            }
        }
        $result[] = new GroupRecord();
        $result[count($result) - 1]->id = -1;
        $result[count($result) - 1]->name = txt('GROUPS_ROOT');
        return $result;
    }
        
    /**
     * rekord készlet beolvasása
     * ha $->userid adott akkor csak azon groupok jelennek meg amelyiknek tagja vagy adminisztrátora
     * @param object $p   userid, parentId 
     * @param int $total
     * @return [ {id,state,avatar,name, childs}, ....]
     *   childs = false ha nincsenek alrekordok
     *   chhilds = [{id,state,avatar,name, childs}...] a legfelső szinten, ha vannak alrekordok
     *   chhilds = [] az alsóbb szinteken, ha vannak alrekordok
     */
    public function getRecords($p, int & $total): array {
        $result = [];
        $total = 0;
        $this->getSubItems($p->parentId, $result, $total, $p->loggedUser->id);
        return $result;
    }
    
    /**
    * echo json str
    *     {"parentId":"###", items:[{"id":###,"name":"xxx", "avatar":"xxx", "childs":bools}, ....]}
    *  @param int $parentId   
    */
    public function getSubGroup(int $parentId): string {
        $result = '{"parentId":"'.$parentId.'", "items": [';
        $table = new table('groups');
        $table->where(['parent','=',$parentId]);
        $recs = $table->get();
        $i = 0;
        foreach ($recs as $rec) {
            if ($i > 0) {
                $result .= ',';
            }
            $table = new Table('groups');
            $table->where(['parent','=',$rec->id]);
            $subrecs = $table->get();
            if (count($subrecs) > 0) {
                $childs = 'true';
            } else {
                $childs = 'false';
            }
            if ($rec->avatar == '') {
                $avatar = "templates/default/no-image.png";
            } else {
                $avatar = $rec->avatar;
            }
            
            $result .= '{"id": '.$rec->id.', "name": "'.$rec->name.'", "avatar":"'.$avatar.'", "childs": '.$childs.'}';
            $i++;
        }
        return $result. ']}';
    }
    
    public function getRecords_by_user(int $offset, int $limit, string $filterStr,
        string $orderField, string $orderDir, int $userId, int &$total): array {
        $filter = new Filter('members','m');
        $filter->join('LEFT OUTER JOIN','groups','g','m.object_id = g.id');
        $filter->where(['m.type','=','groups']);
        $filter->where(['m.user_id','=',$userId]);
        if ($filterStr != '') {
            $filter->where(['g.name','like','%'.$filterStr.'%']);
        }
        $filter->setColumns('distinct g.id, g.name, g.state, m.state userstate');
        $filter->offset($offset);
        $filter->limit($limit);
        $filter->order($orderField.' '.$orderDir);
        $total = $filter->count();
        return $filter->get();
    }
    
    /**
     * csoport statusz automatikus modositása a like számok alapján
     * @param int $id
     */
    public function autoUpdate(int $id) {
        $group = $this->getRecord($id);
        if (($group->id > 0) & (($group->state == 'proposal') | ($group->state == 'active'))) {
            $table = new Table('memebrs');
            $table->where(['type','==','groups']);
            $table->where(['object_id','==',$id]);
            $table->where(['state','==','active']);
            $memberCount = $table->count();
            $table = new Table('memebrs');
            $table->where(['type','==','groups']);
            $table->where(['object_id','==',$id]);
            $table->where(['state','==','admin']);
            $memberCount = $memberCount + $table->count();
            
            $table = new Table('likes');
            $table->where(['type','==','groups']);
            $table->where(['object_id','==',$id]);
            $table->where(['like_type','==','like']);
            $likeCount = $table->count();
            
            $table = new Table('likes');
            $table->where(['type','==','groups']);
            $table->where(['object_id','==',$id]);
            $table->where(['like_type','==','dislike']);
            $dislikeCount = $table->count();
            
            if (($group->state == 'proposal') &
                ((($likeCount - $dislikeCount) >= $group->mgroup_to_active) |
                    ($likeCount >= $memberCount)
                    )
                ) {
                    $group->state = 'active';
                    $table = new table('groups');
                    $table->update($grop);
            }
            if (($group->state == 'active') &
                    ((($dislikeCount - $likeCount) >= ($memberCount * $group->group_to_close / 100)) |
                        ($dislikeCount >= $memberCount)
                        )
               ) {
                    $group->state = 'closed';
                    $table = new table('groups');
                    $table->update($grop);
            }
        }
    }
    
    /**
     * utolsó néhány projekt lekérdezése
     * @param $limit
     * @return array of ProjectRecords
     */
    public function newGroups(int $limit = 3) {
        $table = new Table('groups');
        $table->order('id DESC');
        $table->limit($limit);
        return $table->get();
    }
    
} // class
?>
