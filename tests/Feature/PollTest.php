<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Poll;
use App\Http\Controllers\PollController;
use GrahamCampbell\ResultType\Result;

class PollTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new PollController();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $member = new \App\Models\Member();
        $team = new \App\Models\Team();
        $poll = new \App\Models\Poll();
        
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
    
    public function test_index_notLogged() {
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $view = $this->controller->index('teams', $team->id,'proposal-debates');
        $viewData = $view->getData();
        // ['data'] paginator Result   -- total(),  --  items() /from current page/
        $viewName = $view->getName();
        $this->assertEquals( 'poll.index', $viewName);
        $this->assertEquals( 0, $viewData['data']->total());
    }
    
    public function test_create_notLogged() {
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $redirect = $this->controller->create('teams', $team->id, 'proposal-debates');
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
    }
    
    public function test_create_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        \Auth::loginUsingId($user->id, TRUE);
        $redirect = $this->controller->create('teams',$team->id,'proposal-debates');
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
    }
    public function test_create_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        \Auth::loginUsingId($user->id, TRUE);
        $view = $this->controller->create('teams',$team->id,'proposal-debates');
        $this->assertEquals( 'poll.form', $view->getName());
    }

    public function test_store_notLogged() {
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams", "parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'teams/') );
    }

    public function test_store_logged_notMember() {
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams","parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'teams/') );
    }

    public function test_store_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams", "parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description",
            "statuses" => "proposal-debates",
            'liquied' => 0,
            'debateStart' => 10,
            'optionActivate' => 10,
            'debateDays' => 10,
            'voteDays' => 10,
            'valid' => 10
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'polls/') );
    }

    
    public function test_show_found() {
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $view = $this->controller->show($poll);
        $this->assertEquals('poll.show', $view->getName());
    }
    
    public function test_index_found() {
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $view = $this->controller->index('teams', $team->id, 'proposal-debate');
        $viewData = $view->getData();
        // ['data'] paginator Result   -- total(),  --  items() /from current page/
        $viewName = $view->getName();
        $this->assertEquals( 'poll.index', $viewName);
        $this->assertEquals( 1, $viewData['data']->total());
    }


    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $view = $this->controller->edit($poll);
        $this->assertEquals('poll.form', $view->getName());
    }
    
    public function test_edit_loggedNotAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $redirect = $this->controller->edit($poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
        $redirect = $this->controller->edit($poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
    }
        
        
    public function test_update_notLogged() {
        \Auth::logout();
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams", "parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description javítva",
            "statuses" => "proposal-debates",
            'liquied' => 0,
            'debateStart' => 0,
            'optionActivate' => 0,
            'debateDays' => 0,
            'voteDays' => 0,
            'valid' => 0
        ]);

        $redirect = $this->controller->update($request, $poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
        $model = new \App\Models\Poll();
        $testPoll = $model->where('name','=','testPoll')->first();
        $this->assertEquals( "testPoll description", $testPoll->description );
    }
    
    public function test_update_logged_notAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams", "parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description javítva",
            "statuses" => "proposal-debates",
            'liquied' => 0,
            'debateStart' => 0,
            'optionActivate' => 0,
            'debateDays' => 0,
            'voteDays' => 0,
            'valid' => 0
        ]);

        $redirect = $this->controller->update($request, $poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'poll/list') );
        $model = new \App\Models\Poll();
        $testPoll = $model->where('name','=','testPoll')->first();
        $this->assertEquals( "testPoll description", $testPoll->description );
    }
    
    public function test_update_LoggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
                
        $poll = new \App\Models\Poll();
        $poll = $poll->where('name','=','testPoll')->first();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "parent_type" => "teams", "parent" => $team->id,
            "name" => 'testPoll',
            "description" => "testPoll description javitva",
            "statuses" => "proposal-debates",
            'liquied' => 0,
            'debateStart' => 0,
            'optionActivate' => 0,
            'debateDays' => 0,
            'voteDays' => 0,
            'valid' => 0
        ]);

        $redirect = $this->controller->update($request, $poll);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'polls/') );
        $model = new \App\Models\Poll();
        $testPoll = $model->where('name','=','testPoll')->first();
        $this->assertEquals( "testPoll description javitva", $testPoll->description );
    }
    
    public function test_end() {
        // test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $poll = new \App\Models\Poll();
        $team->where('name','like','test%')->delete();
        $testUser1 = $user->where('name','=','testUser1')->first();
        $member->where('user_id','=',$testUser1->id)->delete();
        $testUser2 = $user->where('name','=','testUser2')->first();
        $member->where('user_id','=',$testUser2->id)->delete();
        $user->where('name','like','test%')->delete();
        $poll->where('name','=','testPoll')->delete();
        $this->assertEquals(1,1);
    }
}
