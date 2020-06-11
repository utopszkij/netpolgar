<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/config.php';
include_once './core/database.php';
include_once './tests/mock.php';
include_once './models/users.php';
include_once './controllers/common.php';
include_once './controllers/default.php';

use PHPUnit\Framework\TestCase;


// test Cases
class defaultControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new DefaultController();
        $this->request = new Request();
        $REQUEST = $this->request;
        
    }
    
    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }
    
    public function test_default() {
        $msg = $this->controller->default($this->request);
        $this->assertEquals(true,true); // csak szintaxis ellenörzés
    }
}

