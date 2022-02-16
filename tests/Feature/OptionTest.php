<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request; 
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Poll;
use App\Http\Models\Option;
use App\Http\Controllers\PollController;
use App\Http\Controllers\OptionController;
use GrahamCampbell\ResultType\Result;

class OptionTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    public $testPoll1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new OptionController();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $member = new \App\Models\Member();
        $team = new \App\Models\Team();
        $poll = new \App\Models\Poll();
        $option = new \App\Models\Option();
        
        // esetleg meglévő korábbi test adatok törlése
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
        $poll->where('name','=','testPoll')->delete();
        $option->where('name','=','testOption')->delete();
        
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
        
        // test Poll létrehozása
        $this->testPoll = $poll->create([
			"id" => 0, "parent_type" => "teams","parent" => $testTeam1->id,
            "name" => 'testPoll', "status" => "debate",
            "description" => "testPoll description"
        ]);
        
        $this->assertEquals(1,1);
    }
    
    public function test_create_notLogged() {
        $pollModel = new \App\Models\Poll();
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $redirect = $this->controller->create($poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    
    public function test_create_logged_notMember() {
        $pollModel = new \App\Models\Poll();
        $user = \App\Models\User::where('name','=','testUser2')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        \Auth::loginUsingId($user->id, TRUE);
        $redirect = $this->controller->create($poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    public function test_create_logged_member() {
        $pollModel = new \App\Models\Poll();
        $user = \App\Models\User::where('name','=','testUser1')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        \Auth::loginUsingId($user->id, TRUE);
        $view = $this->controller->create($poll);
        $this->assertEquals( 'option.create', $view->getName());
    }

    public function test_store_notLogged() {
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $request = new Request(["id" => 0, "pollId" => $poll->id,
            "name" => 'testOption',
            "description" => ""
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $request = new Request(["id" => 0, "pollId" => $poll->id,
            "name" => 'testOption',
            "description" => ""
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $request = new Request(["id" => 0, "pollId" => $poll->id,
            "name" => 'testOption',
            "description" => ""
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
        $option = \DB::table('options')->where('name','=','testOption')->first();
        $this->assertEquals('testOption',$option->name);
    }

    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $view = $this->controller->edit($option);
        $this->assertEquals('option.edit', $view->getName());
    }
    
    public function test_edit_loggedNotAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $redirect = $this->controller->edit($option);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $redirect = $this->controller->edit($option);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
        

    public function test_update_notLogged() {
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $request = new Request(["optionId" => $option->id, "pollId" => $poll->id,
            "name" => 'testOption javitva',
            "description" => ""
        ]);
        $redirect = $this->controller->update($request, $option);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $request = new Request(["optionId" => $option->id, "pollId" => $poll->id,
            "name" => 'testOption javitva',
            "description" => ""
        ]);
        $redirect = $this->controller->update($request,$option);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $pollModel = new \App\Models\Poll();
		$poll = $pollModel->where('name','=','testPoll')->first(); 
        $option = new \App\Models\Option();
        $option = $option->where('name','=','testOption')->first();
        $request = new Request(["optionId" => $option->id, "pollId" => $poll->id,
            "name" => 'testOption javitva',
            "description" => ""
        ]);
        $redirect = $this->controller->update($request, $option);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
        $option = \DB::table('options')->where('name','=','testOption javitva')->first();
        $this->assertEquals('testOption javitva',$option->name);
    }
    
    public function test_end() {
        // test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $poll = new \App\Models\Poll();
        $option = new \App\Models\Option();
        $team->where('name','like','test%')->delete();
        $testUser1 = $user->where('name','=','testUser1')->first();
        $member->where('user_id','=',$testUser1->id)->delete();
        $testUser2 = $user->where('name','=','testUser2')->first();
        $member->where('user_id','=',$testUser2->id)->delete();
        $user->where('name','like','test%')->delete();
        $poll->where('name','=','testPoll')->delete();
        $option->where('name','=','testOption')->delete();
        $this->assertEquals(1,1);
    }
}