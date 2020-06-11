<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include_once './core/phpmailer/src/Exception.php';
include_once './core/phpmailer/src/PHPMailer.php';
include_once './core/phpmailer/src/SMTP.php';

class UserRecord {
   public $id = 0;
   public $nick = 'guest'; // becenév
   public $pswhash = ''; // helszó hash
   public $enabled = 1; // engedélyezett
   public $errorcount = 0; // hibás login kisérlet számláló
   public $block_time = ''; // ekkor lett limitet tullépő hibás kisérlet miatt blokkolva
   public $name = ''; // valós név
   public $email = ''; // user e-mail címe
   public $avatar = ''; // img-re mutató url vagy üres
   public $reg_mode = ''; // 'uklogin' | 'web' | 'facebook' | 'google'
   public $code = ''; // aktiváló kód
}

class UserRightsRecord {
    public $user_id = 0;
    public $right = ''; // 'admin','auditor','registered',....
}

class UsersModel {
    function __construct() {
        $db = new DB();
        $db->createTable('users',
            [['id','INT',11,true],
                ['nick','VARCHAR',32,false],
                ['pswhash','VARCHAR',256,false],
                ['enabled','BOOL','',false],
                ['name','VARCHAR',80,false],
                ['email','VARCHAR',80,false],
                ['avatar','VARCHAR',80,false],
                ['reg_mode','VARCHAR',32,false],
                ['code','VARCHAR',256,false],
                ['errorcount','INT',2,false],
                ['block_time','VARCHAR',32,false]
            ],
            ['nick','email']
        );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create users table '.$db->getErrorMsg(); exit();
        }

        $db->createTable('user_rights',
            [['user_id','INT',11,false],
             ['right','VARCHAR',32,false],
                // admin, g#### -groupAdmin, p#### -projectAdmin,
                // e#### -eventAdmin, c#### -chatAdmin
                // ahol #### az érintett csoport/projekt/chat/event  id -je
            ],
            ['user_id','right']
        );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create users table '.$db->getErrorMsg(); exit();
        }

        $db->createTable('hacker',
            [['ip','VARCHAR',128,false],
             ['errorcount','INT',11,false],
             ['error_time','VARCHAR',32]
            ],
            ['ip']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create hacker table '.$db->getErrorMsg(); exit();
        }

        $table = DB::Table('users');
        if ($table->count() == 0) {
            // admin user felvitele
            $user = new UserRecord;
            $user->id = 0;
            $user->nick='admin';
            $user->pswhash = hash('sha256','123456');
            $user->name='system administrator';
            $user->email = '';
            $user->avatar = '';
            $user->errorcount = 0;
            $user->reg_mode = 'web';
            $user->enabled = true;
            $user->code = '';
            $table->insert($user);
            if ($table->getErrorMsg() != '') {
                echo 'Fatal error in insert admin record '.$table->getErrorMsg(); exit();
            }

            $userRights = new UserRightsRecord();
            $userRights->user_id = $table->getInsertedId();
            $userRights->right = 'admin';
            $table = new Table('user_rights');
            $table->insert($userRights);
            if ($table->getErrorMsg() != '') {
                echo 'Fatal error in insert userRights record '.$table->getErrorMsg(); exit();
            }

        } else {
            // 10 óránál régebbi blokkolások feloldása
            $table->where(['enabled','=',0]);
            $table->where(['block_time','<',date('Y-m-d H:i:s', time() - 36000)]);
            $rec = new stdClass();
            $rec->enabled = 1;
            $rec->block_time = '';
            $table->update($rec);
            if ($db->getErrorMsg() != '') {
                echo 'Fatal error in clear old account block '.$db->getErrorMsg(); exit();
            }

            // 10 óránál régepbbi IP blokkolások törlése
            $table = new Table('hacker');
            $table->where(['error_time','<',date('Y-m-d H:i:s', time() - 36000)]);
            $table->delete();
            if ($db->getErrorMsg() != '') {
                echo 'Fatal error in clear old IP block '.$db->getErrorMsg(); exit();
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

        // kötelező mezők ellenörzése (pswhash -t a controller ellnörzi)
        if (($data->id == 0) && ($data->nick == '')) {
            $msgs[] = 'NICK_REQUED';
        }
        if ($data->name == '') {
            $msgs[] = 'NAME_REQUED';
        }
        if ($data->email == '') {
            $msgs[] = 'EMAIL_REQUED';
        }
        // nick egyediség ellenörzése
        if ($data->id == 0) {
            $res = $this->getByNick($data->nick);
            if (($res->nick != '') && ($res->id != $data->id)) {
                $msgs[] = 'NICK_EXISTS';
            }
        }
        // email egyediség ellenörzése
        $res = $this->getByEmail($data->email);
        if (($res->nick != '') && ($res->id != $data->id)) {
            $msgs[] = 'EMAIL_EXISTS';
        }
        return $msgs;
    }

    /**
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

    protected function sendMail(string $email, string $subject, string $body): bool {
        $result = false;
        $mail = new PHPMailer(true);
        if (config('smtpHost') == '') {
            echo 'fatal error smtp host not defined<br />to:'.$email.'<br>'.$body; exit();
            return $result;
        }
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = config('smtpHost');
            $mail->SMTPAuth = true;
            $mail->Username = config('smtpUser');
            $mail->Password = config('smtpPsw');
            $mail->SMTPSecure = config('smtpSecure');
            $mail->Port = config('smtpPort');
            $mail->setFrom(config('smtpSender'));
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->CharSet = 'utf-8';
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $mail->send();
            $result = true;
        } catch (Exception $e) {
echo 'fatal error in send email <br />'.$mail->ErrorInfo.'<br />to:'.config('smtpHost'); exit();
            $result = false;
        }
        return $result;
    }

    /**
     * user aktiváló email kiküldése
     * @param string $nick
     * @return bool   sikeres vagy nem?
     */
    public function sendActivateEmail(string $nick): bool {
        $result = true;
        $table = new Table('users');
        $table->where(['nick','=',$nick]);
        $old = $table->first();
        $email = $old->email;
        $rec = new stdClass();
        $rec->code = rand(100000,999999).$old->id;
        $table = new Table('users');
        $table->where(['nick','=',$nick]);
        $table->update($rec);
        if ($table->getErrorNum() != 0) {
            echo 'Fatal error user update '.$table->getErrorMsg(); exit();
        }
        if ($table->getErrorNum() == 0) {
            // levél küldés
            $result = true;
            if (config('smtpUser') != '') {
                $url = str_replace('.','&#46;',MYDOMAIN).'/opt/users/activate/code/'.$rec->code;
                $body    = '<p>Köszönjük, hogy regisztrált a netpolgár rendszerbe.</p>'.
                    '<p>A fiók aktiválásához kattintson az alábbi linkre!</p>'.
                    '<p><a href="'.$url.'">'.$url.'</a></p>';
                $subject = 'netpolgar regisztracio aktivalas';
                $this->sendMail($email, $subject, $body);
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * elfelejtett nick és jelszó emlékeztető email
     * @param string $email
     * @param string $nick
     * @param string $psw
     */
    public function sendNickPswEmal(string $email, string $nick, string $psw) {
        $subject = 'netpolgar bejelentkezesi info';
        $body    = '<h2>netpolgar rendszer belepesi adatok</h2>'.
            '<p>Belépési név:<strong>'.$nick.'</strong></p>'.
            '<p>Ideiglenes jelszó:<strong>'.$psw.'</strong></p>'.
            '<p>&nbsp;</p>'.
            '<p>Kérjük, hogy bejelentkezés után változtassa meg a jelszót!</p>';
        $this->sendMail($email, $subject, $body);
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

    public function update(UserRecord $rec): array {
        $result = [];
        $table = new Table('users');
        $old = $table->where(['id','=',$rec->id])->first();
        if ($old) {
            $table = new Table('users');
            $table->where(['id','=',$rec->id]);
            $table->update($rec);
            if ($table->getErrorNum() != 0) {
                $result[] = $table->getErrorMsg();
            }
        } else {
            $result[] = 'NOT_FOUND';
        }
        return $result;
    }

    /**
     * user rekord és a hozzá tartozó user_rights rekordoktörlése
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
                $table = new table('user_rights');
                $table->where(['user_id','=',$id])->delete();
            }
            if ($table->getErrorNum() == 0) {
                $result[] = $table->getErrorMsg();
            }
        } else {
            $result[] = 'NOT_FOUND';
        }
        return $result;

    }

    /**
     * user regts rekordok beolvasása
     * @param int $id
     * @return array [right, right, .... ]
     */
    public function getUserRights(int $id): array {
        $table = new Table('user_rights');
        $recs = $table->where(['user_id','=',$id])->get();
        $result = [];
        foreach ($recs as $rec) {
            $result[] = $rec->right;
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
        $rights = $this->getUserRights($id);
        return (in_array('admin', $rights));
    }

    /**
     * user rights adatok tárolása
     * @param int $id
     * @param array $rights [right, right, ....]
     * @return array hibaüzenetek vagy []
     */
    public function saveUserRights(int $id, array $rights): array {
        $oldRecs = $this->getRights($id);
        // felesleges meglévők törlése
        foreach ($oldRecs as $oldRec) {
            if (array_search($oldRec, $rights) === false ) {
                $table = new Table('user_rights');
                $table->where(['user_id','=',$id])
                ->where(['right','=',$oldRec])
                ->delete();
            }
        }
        // újak felvitele
        foreach ($rights as $right) {
            if (array_search($right, $oldRecs) === false ) {
                $newRec = new UserRightsRecord();
                $newRec->user_id = $id;
                $newRec->right = $right;
                $table = new Table('user_rights');
                $table->insert($newRec);
            }
        }
    }

    /**
     * ellenörzia "hacker" táblában hogy a távoli IP nincs-e blokkolva?
     * ha blokkolva van akkor hibaüzenet és exit
     */
    public function checkIpBlocked() {
        $table = new Table('hacker');
        $table->where(['ip','=',$_SERVER["REMOTE_ADDR"]]);
        $rec = $table->first();
        if ($rec) {
            if ($rec->errorcount > config('falseLoginLimit')) {
                echo 'This REMOTE IP IS BLOCKED ';
                exit();
            }
        }
    }

    /**
     * Ha a távoli IP szerepelt a "hacker" táblában akkor törli onnan
     */
    public function clearIpBlocked() {
        $table = new Table('hacker');
        $table->where(['ip','=',$_SERVER["REMOTE_ADDR"]]);
        $table->delete();
    }

    /**
     * A távoli IP errorcount -ját növeli a "hacker" táblában, illetve ha még nincs ott akkor tárolja
     */
    public function incIpBlocked() {
        $table = new Table('hacker');
        $table->where(['ip','=',$_SERVER["REMOTE_ADDR"]]);
        $rec = $table->first();
        if ($rec) {
            $rec->errorcount = $rec->errorcount + 1;
            $rec->error_time = date('Y-m-d H:i:s');
            $table = new Table('hacker');
            $table->where(['ip','=',$_SERVER["REMOTE_ADDR"]]);
            $table->update($rec);
        } else {
            $rec = new stdClass();
            $rec->ip = $_SERVER["REMOTE_ADDR"];
            $rec->errorcount = 1;
            $rec->error_time = date('Y-m-d H:i:s');
            $table->insert($rec);
        }
    }

    /**
     * rekord sorozat beolvasása
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

} // class
?>
