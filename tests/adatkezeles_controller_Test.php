<?php
declare(strict_types=1);
global $REQUEST;
include_once './tests/mock.php';
include_once './tests/config.php';
include_once './core/database.php';
include_once './models/users.php';
include_once './controllers/adatkezeles.php';

use PHPUnit\Framework\TestCase;


// test Cases
class adatkezelesControllerTest extends TestCase 
{
    protected $controller;
    protected $request;
    protected $client_id;
    
    function __construct() {
        global $REQUEST;
        parent::__construct();
        $this->controller = new AdatkezelesController();
        $this->request = new Request();
        $this->request->set('option','adatkezeles');
        $REQUEST = $this->request;
        
    }
    public function test_start() {
        // create and init test database
        $db = new DB();
        $db->statement('DROP DATABASE test');
        $db->statement('CREATE DATABASE IF NOT EXISTS test');
        $this->assertEquals('',$db->getErrorMsg());
    }
    
    public function test_show() {
        $msg = $this->controller->show($this->request);
        $this->assertEquals(true,true); // csak szintaxis ellenörzés
    }
    
}

