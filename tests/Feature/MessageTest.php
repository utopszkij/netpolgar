<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\User;
use App\Http\Models\Team;
use App\Http\Models\Message;
use App\Http\Controllers\MessageController;
use GrahamCampbell\ResultType\Result;

class MessageTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new MessageController();
    }
    
    public function test_start()  {
        // esetleg meglévő korábbi test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $testUser1 = $user->where('name','=','testUser1')->first();
        if ($testUser1) {
            $member->where('user_id','=',$testUser1->id)->delete();
        }
        $testUser2 = $user->where('name','=','testUser2')->first();
        if ($testUser2) {
            $member->where('user_id','=',$testUser2->id)->delete();
        }
        $team->where('name','like','test%')->delete();
        $user->where('name','like','test%')->delete();
        
        // test userek létrehozása
        $testUser1 = $user->create(['name' => 'testUser1',
            'password' => \Hash::make('testPassword'),
            'email' => 'testUser1email@something.com']);
        $testUser2 = $user->create(['name' => 'testUser2',
            'password' => \Hash::make('testPassword'),
            'email' => 'testUser2email@something.com']);
        
        // testTeam1 record létrehozása
        $testTeam1 = $team->create([
            "parent" => 0,
            "name" => "testTeam1",
            "status" => "active",
            "description" => "testTeam1 description",
            "avatar" => "test1_avatar",
            "config" => "{}",
            "created_by" => $testUser1->id
        ]);
        // testUser1 adminja a testTeam1 -nek
        \App\Models\Member::create([
            'parent_type' => 'teams',
            'parent' => $testTeam1->id,
            'user_id'=> $testUser1->id,
            'rank' => 'admin',
            'status' => 'active',
            'created_by' => $testUser1->id
        ]);
       	$this->assertEquals(1,1);
        	
    }
    
    // szintaktikai tesztek
    
	 public function test_tree() {
	 		$request = new \Illuminate\Http\Request();
			$this->controller->tree($request,'teams',1,0);
        	$this->assertEquals(1,1);
	 }	    
	 public function test_list() {
	 		$request = new \Illuminate\Http\Request();
			$this->controller->list($request,'teams',1,0);
			$this->assertEquals(1,1);
	 }	    
	 public function test_store() {
	 		$request = new \Illuminate\Http\Request([
	        'parent_type' => 'teams',
	        'parent' => '0',
	        'msg_type' => '',
	        'reply_to' => '0',
	        'value' => 'test_message',
	        'messageId' => '0',
	        'backURL' => '/'
	 		]);
			$this->controller->store($request);
			$msg = \DB::table('messages')->where('value','=','test_message')->first();
        	$this->assertNull($msg);
	 }	 
	 public function test_store_notLogged() {
        \Auth::logout();
        
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request([
	        'parent_type' => 'teams',
	        'parent' => $testTeam1->id,
	        'msg_type' => '',
	        'reply_to' => '0',
	        'value' => 'test_message',
	        'messageId' => '0',
	        'backURL' => '/'
	 		]);
			$redirect = $this->controller->store($request);
			$msg = \DB::table('messages')->where('value','=','test_message')->first();
        	$this->assertNull($msg);
	 }	    


	 public function test_store_new_Ok() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
	 	
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request([
	        'parent_type' => 'teams',
	        'parent' => $testTeam1->id,
	        'msg_type' => '',
	        'reply_to' => '0',
	        'value' => 'test_message',
	        'messageId' => '0',
	        'backURL' => '/'
	 		]);
			$redirect = $this->controller->store($request);
			$msg = \DB::table('messages')->where('value','=','test_message')->first();
        	$this->assertEquals($msg->value,'test_message');
	 }	    
	 
	 public function test_moderal_not_logged() {
        \Auth::logout();
		  $msg = \DB::table('messages')->where('value','=','test_message')->first();
		  $redirect = $this->controller->moderal($msg->id);
        $this->assertGreaterThan( 0, strpos($redirect->content(),\URL::to('/')) );
	 }	    

	 public function test_moderal_Ok() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		  $msg = \DB::table('messages')->where('value','=','test_message')->first();
		  $view = $this->controller->moderal($msg->id);
		  $this->assertEquals('message.moderator', $view->getName());
	 }	    

	 public function test_protest_ok() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		  $msg = \DB::table('messages')->where('value','=','test_message')->first();
		  $view = $this->controller->protest($msg->id);
		  $this->assertEquals('message.protest', $view->getName());
	 }	    

	 public function test_saveprotest() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		  $msg = \DB::table('messages')->where('value','=','test_message')->first();
	 		
	 	  $request = new \Illuminate\Http\Request([
	        'parent_type' => 'teams',
	        'parent' => '0',
	        'msg_type' => '',
	        'reply_to' => '0',
	        'value' => 'test_message',
	        'txt' => 'test_message',
	        'messageId' => $msg->id,
	        'backURL' => '/',
	        'moderators' => '10,20',
	 		]);
	 		$this->controller->saveprotest($request);
    		$this->assertEquals(1,1);
	 }	

	 public function test_tree_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$view = $this->controller->tree($request,'teams',$testTeam1->id,0);
		   $this->assertEquals('message.tree', $view->getName());
	 }	    

	 public function test_tree_not_logged() {
        \Auth::logout();
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$view = $this->controller->tree($request,'teams',$testTeam1->id,0);
		   $this->assertEquals('message.tree', $view->getName());
	 }	    

	 public function test_list_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$view = $this->controller->tree($request,'teams',$testTeam1->id,0);
		   $this->assertEquals('message.tree', $view->getName());
	 }	    

	 public function test_list_not_logged() {
        \Auth::logout();
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$view = $this->controller->tree($request,'teams',$testTeam1->id,0);
		   $this->assertEquals('message.tree', $view->getName());
	 }	    
	   
	 public function test_end() {
      // test adatok törlése
      $user = new \App\Models\User();
      $team = new \App\Models\Team();
      $message = new \App\Models\Message();
      $member = new \App\Models\Member();
      $team->where('name','like','test%')->delete();
      $testUser1 = $user->where('name','=','testUser1')->first();
      $member->where('user_id','=',$testUser1->id)->delete();
      $testUser2 = $user->where('name','=','testUser2')->first();
      $member->where('user_id','=',$testUser2->id)->delete();
      $user->where('name','like','test%')->delete();
      $message->where('value','like','test_message')->delete();
     	$this->assertEquals(1,1);
	 }  
    
}
