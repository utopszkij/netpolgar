<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/config.php';
include_once './core/database.php';
include_once './controllers/members.php';
include_once './models/users.php';
include_once './tests/mock.php';

use PHPUnit\Framework\TestCase;

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// test Cases
class membersControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new MembersController();
        $this->request = new Request();
        $REQUEST = $this->request;
    }
    
    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }
    
    public function test_list_notfound() {
        $this->request->set('type','group');
        $this->request->set('objectid','0');
        $this->controller->list($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_list_admin() {
        // create admin user
        // admin user beállítása
        $table = new Table('users');
        $table->delete();
        $user = new UserRecord();
        $user->id = 1;
        $user_right = new stdClass();
        $user_right->user_id = 1;
        $user_right->right = 'admin';
        $table->insert($user);
        $table = new Table('user_rights');
        $table->delete();
        $table->insert($user_right);
        $this->request->sessionSet('user',$user);
        
        // create member record
        $member = new MemberRecord();
        $member->type = 'group';
        $member->objectid = '1';
        $member->member_id = '1';
        $member->state = 'active';
        $member->inviteTime = '2019.10.24';
        $member->candidateTime = '';
        $member->activateTime = '';
        $member->excludeTime = '';
        $table = new Table('members');
        $table->insert($member);
        $this->request->set('type','group');
        $this->request->set('objectid','1');

        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        $this->controller->list($this->request);
        $this->expectOutputRegex('/ADD_MEMBER/');
    }

    public function test_list_noadmin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $guestUser = new UserRecord();
        $guestUser->id = 0;
        $this->request->sessionSet('user',$guestUser);
        $this->controller->list($this->request);
        $this->expectOutputRegex('/TOTAL/');
    }
    
    public function test_form_noadmin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        
        $guestUser = new UserRecord();
        $guestUser->id = 0;
        $this->request->sessionSet('user',$guestUser);
        $this->controller->form($this->request);
        $this->expectOutputRegex('/ACCESS_VIOLATION/');
    }
    
    public function test_form_notfound_admin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','2');
        
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        $this->controller->form($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    
    public function test_form_admin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        $this->controller->form($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_add_notadmin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        
        $guestUser = new UserRecord();
        $guestUser->id = 0;
        $this->request->sessionSet('user',$guestUser);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/ACCESS_VIOLATION/');
    }
    
    public function test_add_groupNotFound_admin() {
        $this->request->set('type','group');
        $this->request->set('objectid','2');
        
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_add_admin() {
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/ADD_MEMBER/');
    }
    
    public function test_save_ok() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/SAVED/');
    }
    
    public function test_save_memberNotFound() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','5');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_save_memberEmpty() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','0');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/SAVED/');
    }
    
    public function test_save_objectidEmpty() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','0');
        $this->request->set('memberid','1');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/MEMBER_REQUED/');
    }
    
    public function test_save_typeEmpty() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/TYPE_EMPTY/');
    }
    
    
    public function test_save_notadmin() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 4;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->save($this->request);
        $this->expectOutputRegex('/ACCESS_VIOLATION/');
    }
    
    public function test_remove_ok() {
        // admin user
        $adminUser = new UserRecord();
        $adminUser->id = 1;
        $this->request->sessionSet('user',$adminUser);
        
        $this->request->set('type','group');
        $this->request->set('objectid','1');
        $this->request->set('memberid','1');
        $this->request->sessionSet('csrToken','123');
        $this->request->set('123','1');
        $this->controller->remove($this->request);
        $this->expectOutputRegex('/REMOVED/');
    }
    
    
    public function test_end() {
        $table = new Table('users');
        $table->delete();
        $table = new Table('members');
        $table->delete();
        $this->assertEquals(1,1);
    }
}

