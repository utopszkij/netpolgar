<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/config.php';
include_once './core/database.php';
include_once './controllers/users.php';
include_once './tests/mock.php';

use PHPUnit\Framework\TestCase;

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// test Cases
class userstControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new UsersController();
        $this->request = new Request();
        $REQUEST = $this->request;
    }
    
    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }

    /*
     * regist task
     * - regomd selektort rajzol ki
     * - mindkét féle regist képernyőt rejtetten kirajzolja
     * - JS kod user selecttől függöen az egyiket teszi láthatóvá
    */
    public function test_regist() {
        $this->controller->regist($this->request);
        $this->expectOutputRegex('/id="regmodSelect"/');
    }

    public function test_nick_empty() {
        $this->request->set('id',0);
        $this->request->set('nick', '');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/NICK_REQUED/');
    }
    
    public function test_email_empty() {
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user');
        $this->request->set('email', '');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/EMAIL_REQUED/');
    }
    
    public function test_add_psw_empty() {
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','');
        $this->request->set('psw2','');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/PSWS_SORT/');
    }
    
    public function test_add_name_empty() {
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', '');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/NAME_REQUED/');
    }
    
    public function test_psws_not_euals() {
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','12ffdfg3456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/PSWS_NOT_EQUALS/');
    }
        
    public function test_add_ok() {
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/NEW_USER_SAVED/');
    }
    
    public function test_add_email_exist() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test1';
        $t->name = 'teszt user';
        $t->email = 'test1@email.hu';
        $table->insert($t);
        
        $this->request->set('id',0);
        $this->request->set('nick', 'test2');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user 2');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/EMAIL_EXISTS/');
    }
    
    public function test_add_nick_exist() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test1';
        $t->name = 'teszt user';
        $t->email = 'test1@email.hu';
        $table->insert($t);
        
        $this->request->set('id',0);
        $this->request->set('nick', 'test1');
        $this->request->set('enabled', 0);
        $this->request->set('errorcount', 0);
        $this->request->set('block_time', '');
        $this->request->set('name', 'teszt user 2');
        $this->request->set('email', 'test1@email.hu');
        $this->request->set('avatar', '');
        $this->request->set('reg_mode', 'web');
        $this->request->set('psw','123456');
        $this->request->set('psw2','123456');
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/NICK_EXISTS/');
    }
    
    public function test_login() {
        $this->controller->login($this->request);
        $this->expectOutputRegex('/NICK/');
    }
    
    public function test_dologin_NOTFOUND() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','nincs');
        $this->request->set('psw','123456');
        $this->controller->dologin($this->request);
        $this->expectOutputRegex('/FALSE_LOGIN/');
    }

    public function test_dologin_FALSE_PSW() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test3';
        $t->name = 'teszt user';
        $t->email = 'test3@email.hu';
        $t->enabled = 1;
        $t->pswhash = hash('sha256','123456');
        $table->insert($t);
        
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test3');
        $this->request->set('psw','12345678');
        $this->controller->dologin($this->request);
        $this->expectOutputRegex('/FALSE_LOGIN/');
    }
    
    
    public function test_dologin_blocked() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test4';
        $t->name = 'teszt user';
        $t->email = 'test4@email.hu';
        $t->enabled = 0;
        $t->block_time = date('Y-m-d H:i:s');
        $t->pswhash = hash('sha256','123456');
        $table->insert($t);
        
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test4');
        $this->request->set('psw','123456');
        $this->controller->dologin($this->request);
        $this->expectOutputRegex('/ACCOUNT_IS_BLOCKED/');
    }
    
    public function test_dologin_Too_many_falseLogin() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test5';
        $t->name = 'teszt user';
        $t->email = 'test5@email.hu';
        $t->enabled = 1;
        $t->errorcount = 10;
        $t->block_time ='';
        $t->pswhash = hash('sha256','123456');
        $table->insert($t);
        
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test5');
        $this->request->set('psw','rossz');
        $this->controller->dologin($this->request);
        $this->expectOutputRegex('/TOO_MANY_FALSELOGIN/');
    }
       
    public function test_dologin_OK() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 0;
        $t->nick = 'test2';
        $t->name = 'teszt user';
        $t->email = 'test1@email.hu';
        $t->enabled = 1;
        $t->pswhash = hash('sha256','123456');
        $table->insert($t);
        
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test2');
        $this->request->set('psw','123456');
        $this->controller->dologin($this->request);
        $this->expectOutputRegex('/redirect/');
    }
    
    public function test_activate_notok() {
        $this->request->set('code','1234567890');
        $this->controller->activate($this->request);
        $this->expectOutputRegex('/FALSE_ACTIVATION/');
    }
    
    public function test_activate_ok() {
        // isert egy teszt rekord
        $table = new table('users');
        $t = new UserRecord();
        $t->id = 80;
        $t->nick = 'test80';
        $t->name = 'teszt user';
        $t->email = 'test80@email.hu';
        $t->enabled = 0;
        $t->pswhash = hash('sha256','123456');
        $t->code = '12345680';
        $table->insert($t);
        
        $this->request->set('code','12345680');
        $this->controller->activate($this->request);
        $this->expectOutputRegex('/ACCOUNT_ACTIVATED/');
    }
    
    
    public function test_forgetnick() {
        $this->controller->forgetnick($this->request);
        $this->assertEquals(1,1); // only syntax check
    }
    
    public function test_forgetpsw_nick() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test80');
        $this->controller->forgetpsw($this->request);
        $this->expectOutputRegex('/EMAIL_SENDED/');
    }
    
    public function test_forgetpsw_email() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('email','test80@email.hu');
        $this->controller->forgetpsw($this->request);
        $this->expectOutputRegex('/EMAIL_SENDED/');
    }
        
    public function test_forgetpsw_notfound() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('email','nincs');
        $this->controller->forgetpsw($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
        
    public function test_getactivateemail() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test80');
        $this->controller->getactivateemail($this->request);
        $this->expectOutputRegex('/EMAIL_SENDED/');
    }
    
    public function test_getactivateemail_notfound() {
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->request->set('nick','test99');
        $this->controller->getactivateemail($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_logout() {
        $this->controller->logout($this->request);
        $this->assertEquals(1,1); // only syntax check
    }
    
    public function test_profile_notfound(){
        $this->request->sessionSet('user',new UserRecord);
        $this->controller->profile($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }    
    
    public function test_profile_ok(){
        $table = new Table('users');
        $user = $table->first();
        $this->request->sessionSet('user',$user);
        $this->controller->profile($this->request);
        $this->expectOutputRegex('/PROFILE/');
    }
    
    public function test_removeaccount_notfound() {
        $this->request->sessionSet('user',new UserRecord());
        $this->controller->removeaccount($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_removeaccount_ok(){
        $table = new Table('users');
        $user = $table->where(['id','>',1])->first();
        $this->request->sessionSet('user',$user);
        $this->controller->removeaccount($this->request);
        $this->expectOutputRegex('/SURE_REMOVE_ACCOUNT/');
    }
    
    public function test_doremoveaccoun_notfoundt() {
        $this->request->sessionSet('user',new UserRecord());
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->doremoveaccount($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_doremoveaccount_ok(){
        $table = new Table('users');
        $user = $table->where(['id','>',1])->first();
        $this->request->sessionSet('user',$user);
        $this->request->sessionSet('csrToken','testcsrtoken');
        $this->request->set('testcsrtoken',1);
        $this->controller->doremoveaccount($this->request);
        $this->expectOutputRegex('/ACCOUNT_REMOVED/');
    }
    
    public function test_list_access_violation() {
        $this->request->sessionSet('user',new UserRecord());
        $this->controller->list($this->request);
        $this->expectOutputRegex('/ACCESS_VIOLATION/');
    }
    
    public function test_list_ok() {
        $table = new Table('users');
        $adminUser = $table->where(['id','>',0])->first();
        $this->request->sessionSet('user', $adminUser);
        $this->controller->list($this->request);
        $this->expectOutputRegex('/USERS_LIST/');
    }
    
    
    public function test_end() {
        $table = new Table('users');
        $table->delete();
        $this->assertEquals(1,1);
    }
}

