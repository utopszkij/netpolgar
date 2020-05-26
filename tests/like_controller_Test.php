<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/config.php';
include_once './core/database.php';
include_once './controllers/like.php';
include_once './tests/mock.php';

use PHPUnit\Framework\TestCase;


// test Cases
class LikeControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new LikeController();
        $this->request = new Request();
        $REQUEST = $this->request;
        
    }

    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }
    
    public function test_show() {
        $this->controller->show('groups',1,'label','');
        $this->expectOutputRegex('/label/');
    }
    
    public function test_list() {
        $this->controller->list($this->request);
        $this->expectOutputRegex('/LIKE/');
    }
    
    public function test_likesow() {
        $this->request->set('type','groups');
        $this->request->set('id','1');
        $this->controller->likeshow($this->request);
        $this->expectOutputRegex('/\{/');
    }
    
    public function test_likeupclick() {
        $this->request->set('type','groups');
        $this->request->set('id','1');
        $this->controller->likeupclick($this->request);
        $this->expectOutputRegex('/\{/');
    }
    
    public function test_likedownclick() {
        $this->request->set('type','groups');
        $this->request->set('id','1');
        $this->controller->likedownclick($this->request);
        $this->expectOutputRegex('/\{/');
    }
    
    
}

