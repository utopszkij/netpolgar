<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Team;
use App\Http\Controllers\TeamController;
use GrahamCampbell\ResultType\Result;

class TeamTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new TeamController();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        
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
        $view = $this->controller->index(0);
        $viewData = $view->getData();
        // ['data'] paginator Result   -- total(),  --  items() /from current page/
        // ['parent'] string
        // ['info'] object
        $viewName = $view->getName();
        $this->assertEquals( 'team.index', $viewName);
        $this->assertGreaterThan( 0, $viewData['data']->total());
        $this->assertEquals( 0, count($viewData['info']->userRank));
        $this->assertEquals( 0, count($viewData['info']->userParentRank));
    }
    
    public function test_index_Logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $view = $this->controller->index(0);
        $viewData = $view->getData();
        $viewName = $view->getName();
        $this->assertEquals( 'team.index', $viewName);
        $this->assertGreaterThan( 0, $viewData['data']->total());
        $this->assertEquals( 'active_member', $viewData['info']->userRank[0]);
        $this->assertEquals( 'active_member', $viewData['info']->userParentRank[0]);
    }
    
    public function test_create_notLogged() {
        \Auth::logout();
        $redirect = $this->controller->create(0);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_create_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $view = $this->controller->create(0);
        $this->assertEquals( 'team.form', $view->getName());
    }
    
    public function test_show_found() {
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $view = $this->controller->show($team);
        $this->assertEquals('team.show', $view->getName());
    }
    
    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $view = $this->controller->edit($team);
        $this->assertEquals('team.form', $view->getName());
    }
    
    public function test_edit_loggedNotAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $redirect = $this->controller->edit($team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $redirect = $this->controller->edit($team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
        
    public function test_store_notLogged() {
        \Auth::logout();
        $request = new Request(["id" => 0, "parent" => "0",
            "name" => 'testTeam2',
            "description" => "testTeam2 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar'
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_store_Logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $request = new Request(["id" => 0, "parent" => "0",
            "name" => 'testTeam2',
            "description" => "testTeam2 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
				'close' => 1,         
				'memberActivate' => 1,
				'memberExclude' => 1,
				'rankActivate' => 1,
				'rankClose' => 1,
				'projectActivate' => 1,
				'productActivate' => 1,
				'subTeamActivate' => 1,
				'debateActivate' => 1
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
        
        $model = new \App\Models\Team();
        $testTeam2 = $model->where('name','=','testTeam2')->first();
        $this->assertTrue( is_object($testTeam2) );
    }
        
    public function test_update_notLogged() {
        \Auth::logout();
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $request = new Request(["parent" => "0",
            "id" => $team->id,
            "name" => 'testTeam1',
            "description" => "testTeam1 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
				'close' => 1,         
				'memberActivate' => 1,
				'memberExclude' => 1,
				'rankActivate' => 1,
				'rankClose' => 1,
				'projectActivate' => 1,
				'productActivate' => 1,
				'subTeamActivate' => 1,
				'debateActivate' => 1
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
        $model = new \App\Models\Team();
        $testTeam1 = $model->where('name','=','testTeam1')->first();
        $this->assertEquals( "testTeam1 description", $testTeam1->description );
    }
    
    public function test_update_logged_notAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $request = new Request(["parent" => "0",
            "id" => $team->id,
            "name" => 'testTeam1',
            "description" => "testTeam1 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
				'close' => 1,         
				'memberActivate' => 1,
				'memberExclude' => 1,
				'rankActivate' => 1,
				'rankClose' => 1,
				'projectActivate' => 1,
				'productActivate' => 1,
				'subTeamActivate' => 1,
				'debateActivate' => 1
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
        $model = new \App\Models\Team();
        $testTeam1 = $model->where('name','=','testTeam1')->first();
        $this->assertEquals( "testTeam1 description", $testTeam1->description );
    }
    
    public function test_update_LoggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
                
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $request = new Request(["parent" => "0",
            "id" => $team->id,
            "name" => 'testTeam1',
            "description" => "testTeam1 description javitva",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
				'close' => 1,         
				'memberActivate' => 1,
				'memberExclude' => 1,
				'rankActivate' => 1,
				'rankClose' => 1,
				'projectActivate' => 1,
				'productActivate' => 1,
				'subTeamActivate' => 1,
				'debateActivate' => 1
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
        $model = new \App\Models\Team();
        $testTeam1 = $model->where('name','=','testTeam1')->first();
        $this->assertEquals( "testTeam1 description javitva", $testTeam1->description );
    }
    
    public function test_destroy() {
       // csak szintaktikai teszt, nincs használva ez a method 
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $this->controller->destroy($team);
        $this->assertEquals(1,1);
    }
    
    public function test_checkStatus() {
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $model = new \App\Models\Team();
        $model->checkStatus($team->id);
        $this->assertEquals(1,1);
    }
    
    public function test_end() {
        // test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $team->where('name','like','test%')->delete();
        $testUser1 = $user->where('name','=','testUser1')->first();
        $member->where('user_id','=',$testUser1->id)->delete();
        $testUser2 = $user->where('name','=','testUser2')->first();
        $member->where('user_id','=',$testUser2->id)->delete();
        $user->where('name','like','test%')->delete();
        $this->assertEquals(1,1);
    }
}
