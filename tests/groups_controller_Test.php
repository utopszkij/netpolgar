<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/config.php';
include_once './core/database.php';
include_once './tests/mock.php';
include_once './models/users.php';
include_once './controllers/common.php';
include_once './controllers/groups.php';

use PHPUnit\Framework\TestCase;

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// test Cases
class groupsControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new GroupsController();
        $this->request = new Request();
        $this->request->set('otion','groups');
        $REQUEST = $this->request;
    }
    
    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }

    public function test_save_ok() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
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
        
        // group rekord beállítása
        $this->request->set('id',0);
        $this->request->set('name','test1 group');
        $this->request->set('description','test1 group');
        $this->request->set('reg_mode','self');
        $this->request->set('state','active');
        $this->request->set('parent', 0);
        $this->request->set('group_to_active',10);
        $this->request->set('group_to_close',80);
        $this->request->set('member_to_active',2);
        $this->request->set('member_to_exclude',90);
        
        $this->controller->save($this->request);
        $this->expectOutputRegex('/SAVED/');
    }

    public function test_save_checkerror1() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        
        // group rekord beállítása
        $this->request->set('id',0);
        $this->request->set('name','');
        $this->request->set('description','test1 group');
        $this->request->set('reg_mode','self');
        $this->request->set('state','active');
        $this->request->set('parent', 0);
        $this->request->set('group_to_active',10);
        $this->request->set('group_to_close',80);
        $this->request->set('member_to_active',2);
        $this->request->set('member_to_exclude',90);
        
        $this->request->set('parents',[]);
        $this->controller->save($this->request);
        $this->expectOutputRegex('/NAME_REQUED/');
    }
    
    
    public function test_save_checkerror2() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        
        // group rekord beállítása
        $this->request->set('id',0);
        $this->request->set('name','test2');
        $this->request->set('description','');
        $this->request->set('reg_mode','self');
        $this->request->set('state','active');
        $this->request->set('parent', 0);
        $this->request->set('group_to_active',10);
        $this->request->set('group_to_close',80);
        $this->request->set('member_to_active',2);
        $this->request->set('member_to_exclude',90);
        
        $this->request->set('parents',[]);
        $this->controller->save($this->request);
        $this->expectOutputRegex('/DESCRIPTION_REQUED/');
    }
    
    
    public function test_save_NUMERROR() {
        $this->request->set('option','groups');
        // ilyenkor is tárol, csak nullákat ir a numerikus mezőkbe

        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        
        // group rekord beállítása
        $this->request->set('id',0);
        $this->request->set('name','test3');
        $this->request->set('description','test3 group');
        $this->request->set('reg_mode','self');
        $this->request->set('state','active');
        $this->request->set('parent', 0);
        $this->request->set('group_to_active','nemjo');
        $this->request->set('group_to_close','nemjo');
        $this->request->set('member_to_active','nem jo');
        $this->request->set('member_to_exclude','nem jo');
        
        $this->request->set('parents',[]);
        $this->controller->save($this->request);
        $this->expectOutputRegex('/SAVED/');
    }
    
    public function test_list() {
        $this->request->set('option','groups');
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        $this->controller->list($this->request);
        $this->expectOutputRegex('/LIST/');
    }
    
    public function test_groupform_ok() {
        $this->request->set('option','groups');
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // groupId meghatározása
        $table = new Table('groups');
        $group = $table->first();
        $groupId = $group->id;
        $this->request->set('groupid', $groupId);

        $this->controller->groupform($this->request);
        $this->expectOutputRegex('/'.$group->name.'/');
    }
    
    public function test_groupform_notfound() {
        $this->request->set('option','groups');
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // groupId meghatározása
        $table = new Table('groups');
        $group = $table->first();
        $groupId = 100 + $group->id;
        $this->request->set('groupid', $groupId);
        
        $this->controller->groupform($this->request);
        $this->expectOutputRegex('/NOT_FOUND/');
    }
    
    public function test_groupform_rootGroup() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('user',$user);
        $groupId = 0;
        $this->request->set('groupid', $groupId);
        $this->controller->groupform($this->request);
        $this->expectOutputRegex('/GROUPS_ROOT/');
    }
    
    public function test_add_ok() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('loggedUser',$user);
        
        $this->request->set('parentid', 0);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/ADD_SUB_GROUP/');
    }
    
    
    public function test_add_notadmin() {
        $this->request->set('option','groups');
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // user not admin
        $user = new UserRecord();
        $user->id = 2;
        $this->request->sessionSet('loggedUser',$user);
        $this->request->set('parentid', 0);
        $this->controller->add($this->request);
        $this->expectOutputRegex('/ACCESS_VIOLATION/');
    }
    
    public function test_remove() {
        $this->request->set('option','groups');
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('loggedUser',$user);
        
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // groupId meghatározása
        $table = new Table('groups');
        $group = $table->first();
        $groupId = $group->id;
        $this->request->set('groupid', $groupId);
        
        $this->controller->remove($this->request);
        $this->expectOutputRegex('/SURE/');
    }
    
    public function test_doremove() {
        $this->request->set('option','groups');
        // user admin
        $user = new UserRecord();
        $user->id = 1;
        $this->request->sessionSet('loggedUser',$user);
        
        // csr token beállítása
        $this->request->set('testCsrToken',1);
        $this->request->sessionset('csrToken','testCsrToken');
        
        // groupId meghatározása
        $table = new Table('groups');
        $group = $table->first();
        $groupId = $group->id;
        $this->request->set('groupId', $groupId);
        
        $this->controller->doremovegroup($this->request);
        $this->expectOutputRegex('/DELETED/');
    }
    
    
    public function test_end() {
        $table = new Table('groups');
        $table->delete();
        $this->assertEquals(1,1);
    }
}

