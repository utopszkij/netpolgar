<?php
class UserRecord {
   public $id = 0;
   public $nick = 'guest'; // becenév
   public $enabled = 1; // engedélyezett
   public $name = ''; // valós név
   public $email = ''; // user e-mail címe
   public $avatar = ''; // img-re mutató url vagy üres
   public $reg_mode = ''; // 'uklogin' | 'web' | 'facebook' | 'google'
   public $admin = 0;
   public $pubinfo = '';
}


class UsersModel {
    function __construct() {
        $db = new DB();
        $db->createTable('users',
            [['id','INT',11,true],
                ['nick','VARCHAR',32,false],
                ['enabled','BOOL','',false],
                ['name','VARCHAR',80,false],
                ['email','VARCHAR',80,false],
                ['avatar','VARCHAR',80,false],
                ['reg_mode','VARCHAR',32,false],
                ['admin', 'INT',1,false],
                ['pubinfo','TEXT',0,false]
            ],
            ['nick','email']
        );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create users table '.$db->getErrorMsg(); exit();
        }

        $table = DB::Table('users');
        if ($table->count() == 0) {
            // admin user felvitele
            $user = new UserRecord;
            $user->id = 0;
            $user->nick='admin';
            $user->name='system administrator';
            $user->email = '';
            $user->avatar = '';
            $user->reg_mode = 'web';
            $user->enabled = true;
            $user->admin = 1;
            $table->insert($user);
            if ($table->getErrorMsg() != '') {
                echo 'Fatal error in insert admin record '.$table->getErrorMsg(); exit();
            }
        }
    } // constructor

    /**
     * user rekord tárolás előtti ellenörzése
     * @param UserRecord $data
     * @param bool $admin
     * @return array hibaüzenetek vagy üres tömb
     */
    public function check(UserRecord $data): array {
        $msgs = [];
        return $msgs;
    }

    /**
     * user rekord felvitele, vagy modositása
     * az eéső tényleges bejelentkezés a második felvitt rekordot eredményezi,
     * ez admin jelzést kap. (az elsőt a constructor vitte fel szintén admin jelzéssel)
     * @param UserRecord $data
     * @return array hibaüzenetek vagy üres tömb
     */
    public function save(UserRecord &$data): array {
        $msgs = [];
        if (isset($data->avatar)) {
            if ($data->avatar == '') {
                $data->avatar = './images/noavatar.png';
            }
            if ($data->avatar == 'gravatar') {
                $data->avatar = 'https://gravatar.com/avatar/'.md5($data->email);
            }
        }
        $table = new table('users');
        if ($data->id == 0) {
            $count = $table->count();
            if ($count <= 1) {
                $data->admin = 1;
            }
            $table->insert($data);
            $data->id = $table->getInsertedId();
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

    protected function obj2UserRecord($obj): UserRecord {
        $result = new UserRecord();
        foreach ($result as $fn => $fv) {
            $result->$fn = $obj->$fn;
        }
        return $result;
    }

    /**
     * user rekord olvasás nick alapján
     * @param string $nick
     * @return UserRecord  nick = '' ha nem található
     */
    public function getById(int $id): UserRecord {
        $table = new Table('users');
        $rec = $table->where(['id','=',$id])->first();
        if ($rec) {
            $result = $this->obj2UserRecord($rec);
        } else {
            $result = new UserRecord();
            $result->nick = '';
        }
        return $result;
    }

    /**
     * user rekord olvasás nick alapján
     * @param string $nick
     * @return UserRecord  nick = '' ha nem található
     */
    public function getByNick(string $nick): UserRecord {
        $table = new Table('users');
        $rec = $table->where(['nick','=',$nick])->first();
        if ($rec) {
            $result = $this->obj2UserRecord($rec);
        } else {
            $result = new UserRecord();
            $result->nick = '';
        }
        return $result;
    }

    /**
     * user rekord olvasás email alapján
     * @param string $nick
     * @return UserRecord  nick = '' ha nem található
     */
    public function getByEmail(string $email): UserRecord {
        $table = new Table('users');
        $rec = $table->where(['email','=',$email])->first();
        if ($rec) {
            $result = $this->obj2UserRecord($rec);
        } else {
            $result = new UserRecord();
            $result->nick = '';
        }
        return $result;
    }

    /**
     * user rekord törlése
     * @param int $id
     * @return array  hibaüzenetek vagy []
     */
    public function remove(int $id): array {
        $result = [];
        $table = new Table('users');
        $rec = $table->where(['id','=',$id])->first();
        if ($rec) {
            $table->delete();
            if ($table->getErrorNum() == 0) {
                $result[] = $table->getErrorMsg();
            }
        } else {
            $result[] = 'NOT_FOUND';
        }
        return $result;

    }

    /**
     * A paraméterben kapott id -ü user systemAdmin?
     * @param Int $id
     * @return bool
     */
    public function isAdmin(Int $id): bool {
        $result = false;
        $rec = $this->getById($id);
        return ($rec->admin == 1);
    }

    /**
     * rekord készlet beolvasása
     * @param object $p  - offset, limit, orderField, orderDir, filterStr
     * @param int $total
     * @return array of UserRecord
     */
    public function getRecords($p, int  &$total): array {
        $table = new Table('users');
        $table->offset($p->offset);
        $table->limit($p->limit);
        $table->order($p->orderField.' '.$p->orderDir);
        if ($p->filterStr != '') {
            // védekezés sql injekció ellen
            $filterStr = '%'.$p->filterStr.'%';
            $filterStr = str_replace('"','\"',$filterStr);
            $filterStr = str_replace('--','%',$filterStr);
            $filterStr = str_replace(' ','%',$filterStr);

            $table->where(['nick','like',$filterStr]);
            $table->orWhere(['name','like',$filterStr]);
         }
        $total = $table->count();
        return $table->get();
    }
    
    /**
     * utolsó néhány projekt lekérdezése
     * @param $limit
     * @return array of ProjectRecords
     */
    public function newUsers(int $limit = 3) {
        $table = new Table('users');
        $table->order('id DESC');
        $table->limit($limit);
        return $table->get();
    }
    

} // class
?>
