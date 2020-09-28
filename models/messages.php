<?php

/* privát üzenetek, és üzenőfal üzenetek (messages és comments modulok használják)  */

class MessageRecord {
    public $id = 0;         // record ID
    public $reply_to = 0;   // erre válasz
    public $level = 0;      // 0: új >0, n-edik szintű válasz.
    public $type = '';      // címzett tipus 'groups','projects'...'users','private'
    public $object_id = 0;  // címzett
    public $sender_type = ''; // feladó tipusa 'groups', 'projects', .... 'users' 
    public $sender_id = 0;  // feladó id
    public $text= '';       // üzenet szövege
    public $send_time = ''; // üzenet létrehozás időpontja
    public $state = '';     // 'inwork','updated', 'moderated'
    public $moderatorinfo = '';
    public $moderator_id = 0;
    public $parent = 0;    // rmegjelenitő fa level <= 2 -nél azonos a $reply_to -val, level > 2 -nél a level=2 reply_to -ja
}
class ReadRecord {
    public $id = 0;
    public $message_id = 0;
    public $user_id = 0;
}

/* FIGYELEM FONTOS 
Ha egy válasz üzenet kerül felvitelre (bármelyik szinten) az a hozzá tartozó $reply_to lánc szerinti
összes elözményt mindenki által olvasatlanra állítja (törli a read rekordokat)

Válasz felvitelénél a level -től függp $parent kitöltésre vigyázni!
*/

class MessagesModel extends Model {
    function __construct() {
        $this->tableName = 'messages';
        $db = new DB();
        $db->createTable('messages',
            [['id','INT',11,true],
                ['reply_to','INT',11,false],
                ['level','INT',11,false],
                ['type','VARCHAR',32,false],
                ['object_id','INT',11,false],
                ['sender_type','VARCHAR',32,false],
                ['sender_id','INT',11,false],
                ['text','TEXT',0,false],
                ['send_time','VARCHAR',10,false],
                ['state','VARCHAR',10,false],
                ['moderatorinfo','VARCHAR',255,false],
                ['moderator_id','INT',11,false],
                ['parent','INT',11,false]
            ],
            ['id','object_id','reply_to','sender_id']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create messages table '.$db->getErrorMsg(); exit();
        }
        $db = new DB();
        $db->createTable('reads',
            [['id','INT',11,true],
                ['message_id','INT',11,false],
                ['user_id','INT',11,false]
            ],
            ['id','message_id']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create reads table '.$db->getErrorMsg(); exit();
        }
        
    }
    
    /**
     * üzenet számok kiolvasása az adatbázisból
     * @param string $type
     * @param int $object_id
     * @param int $userId
     * @return {"total":n, "new": num}
     */
    public function getCounts(string $type, int $object_id, int $userId) {
        $result = new stdClass();
        $filter = new Filter('messages','m');
        $filter->join('LEFT OUTER JOIN', 'reads', 'r', 'r.message_id = m.id');
        $filter->setColumns('distinct m.id');
        $filter->where(['m.type','=',$type]);
        $filter->where(['m.object_id','=',$object_id]);
        $result->total = $filter->count();
        $filter->where(['r.user_id','=',$userId]);
        $result->new = $filter->count();
        return $result;
    }
    
    public function getMergedMessages(int $object_id, string $offset, UserRecord $user) {
        if ($offset == 'new') {
            $db = new DB();
            $sql = 'select m.id, r.user_id, "                                                            " name
            from messages m
            left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
            where m.type="users" and m.object_id = '.$object_id.'
            union all
            select m.id, r.user_id, substr(g.name,0,60)
            from messages m
            left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
            left outer join members me on  me.type=m.type and me.object_id = m.object_id and me.user_id = '.$user->id.'
            left outer join groups g on g.id = m.object_id
            where m.type="groups" and me.user_id = '.$user_id.' and (me.state = "active" or me.state="admin")
            union all
            select m.id, r.user_id, substr(p.name,0,60)
            from messages m
            left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
            left outer join members me on  me.type=m.type and me.object_id = m.object_id and me.user_id = '.$user->id.'
            left outer join projects p on p.id = m.object_id
            where m.type="projects" and me.user_id = '.$user_id.' and (me.state = "active" or me.state="admin")
            union all
            select m.id, r.user_id, substr(t.text,0,60)
            from messages m
            left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
            left outer join tasks t on  m.type="tasks" and t.id = m.object_id and t.nick = '.$user->nick.'
            where m.type="tasks" 
            order by m.send_time
            ';
            $db->setQuery($sql);
            $items = $db->get();
            $offset = 0;
            if (count($items) > 0) {
                while ($i < count($items) & ($offset == 0)) {
                    if ($items[$i]->user_id == $user_id) {
                        $i++;
                    } else {
                        $offset = $i;
                    }
                }
                if (($offset == 0) | ($offset > (count($items) - 5))) {
                    $offset = count($items) - 5;
                }
            }
        }
        $db = new DB();
        $sql = 'select m.*, r.user_id
        from messages m
        left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
        where m.type="users" and m.object_id = '.$object_id.' 
        union all
        select m.*, r.user_id
        from messages m
        left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
        left outer join members me on  me.type=m.type and me.object_id = m.object_id and me.user_id = '.$user->id.'
        where m.type="groups" and me.user_id = '.$user->id.' and (me.state = "active" or me.state="admin")
        union all
        select m.*, r.user_id
        from messages m
        left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
        left outer join members me on  me.type=m.type and me.object_id = m.object_id and me.user_id = '.$user->id.'
        where m.type="projects" and me.user_id = '.$user->id.' and (me.state = "active" or me.state="admin")
        union all
        select m.*, r.user_id
        from messages m
        left outer join reads r on r.message_id = m.id and r.user_id = '.$user->id.'
        left outer join tasks t on  m.type="tasks" and me.object_id = t.id and t.nick = '.$user->nick.'
        where m.type="tasks" 
        order by m.send_time
        limit '.$offset.',5
        ';
        $db->setQuery($sql);
        $result = new stdClass();
        $result->offset = $offset;
        $result->items = $db->get();
        return $result;
    }
    
    /**
     * rekord készlet olvasása
     * ha $offset == 'new' akkor megkeresi a legkorábbi olvasatlant és onnan kezdve olvas öt rekordot
     *    ha nincs olvasatlan akkor az utolsó ötöt olvassa
     * ha $offset egy szám akkor innen kezdve olvas öt rekordot
     * 
     * ha $type == 'users' akkor mergelten olvassa a user + érintett groups, projects, products üzenőfalat 
     * @param string $type
     * @param int $object_id
     * @param int $reply_to  - lehet nulla is
     * @param string $ffset
     * @param int user_id
     * @return {offset, items:[{message.*, r.user_id},...]}
     */
    public function getMessages(string $type, int $object_id, int $parent, string $offset, UserRecord $user) {
        if (($type == 'users') & ($reply_to == 0))  {
           $result = $this->getMergedMessages($object_id, $reply_to, $offset, $user); 
        } else {
            if ($offset == 'new') {
                // meg kell keresni az első olvasatlant és onnan kezdve 5-ötöt kell megjeleníteni
            }
            $result = new stdClass();
            $filter = new Filter('messages','m');
            $filter->join('LEFT OUTER JOIN', 'reads', 'r', 'r.message_id = m.id and r.user_id = '.$user->id);
            $filter->where(['m.type','=',$type]);
            $filter->where(['m.object_id','=',$object_id]);
            $filter->where(['m.parent','=',$parent]);
            $filter->setColumns('distinct m.*, r.user_id');
            $filter->offset($offset);
            $filter->order('m.send_time');
            $filter->limit(5);
            $result->items = $filter->get();
        }
        return $result;
    }

} // class
?>
