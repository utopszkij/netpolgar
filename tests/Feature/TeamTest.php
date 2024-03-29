<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Http\Controllers\TeamController;

if (!defined('UNITTEST')) {
    define('UNITTEST',1);
}

class TeamTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new \App\Http\Controllers\TeamController();
    }

    public function test_start()  {
        $this->controller = new TeamController();
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $like = new \App\Models\Like();
        
        // esetleg meglévő test adatok törlése
        $this->test_end();

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
        $this->assertEquals('Illuminate\View\View', get_class($view));
        
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
        $this->assertEquals('Illuminate\View\View', get_class($view));
        
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
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_create_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $testTeam = \App\Models\Team::where('name','=','TestTeam1')->first();
        $view = $this->controller->create($testTeam->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $this->assertEquals( 'team.form', $view->getName());
    }
    
    public function test_show_found() {
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $view = $this->controller->show($team);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $this->assertEquals('team.show', $view->getName());
    }
    
    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $view = $this->controller->edit($team);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $this->assertEquals('team.form', $view->getName());
    }
    
    public function test_edit_loggedNotAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $redirect = $this->controller->edit($team);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        
        $team = new \App\Models\Team();
        $team = $team->where('name','=','testTeam1')->first();
        $redirect = $this->controller->edit($team);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_store_notLogged() {
        \Auth::logout();
        $request = new Request(["parent" => "0",
            "name" => 'testTeam2',
            "description" => "testTeam2 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar'
        ]);
        $redirect = $this->controller->store($request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/0/teams') );
    }
    
    public function test_store_Logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $testTeam = \App\Models\Team::where('name','=','TestTeam1')->first();
        
        $request = new Request(["parent" => $testTeam->id,
            "name" => 'testTeam2',
            "description" => "testTeam2 description",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
            "close" => 1,
            "memberActivate" => 1,
            "memberExclude" => 1,
            "rankActivate" => 1,
            "rankClose" => 1,
            "projectActivate" => 1,
            "productActivate" => 1,
            "subTeamActivate" => 1,
            "debateActivate" => 1
        ]);
        $redirect = $this->controller->store($request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/') );
        
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
            "avatar" => 'testAvatar'
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
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
            "avatar" => 'testAvatar'
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
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
        $request = new Request(["parent" => $team->parent,
            "id" => $team->id,
            "name" => 'testTeam1',
            "description" => "testTeam1 description javitva",
            "ranks" => "admin, manager",
            "avatar" => 'testAvatar',
            "close" => 1,
            "memberActivate" => 1,
            "memberExclude" => 1,
            "rankActivate" => 1,
            "rankClose" => 1,
            "projectActivate" => 1,
            "productActivate" => 1,
            "subTeamActivate" => 1,
            "debateActivate" => 1
        ]);
        $redirect = $this->controller->update($request, $team);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
        $this->assertGreaterThan( 0, strpos($redirect->content(),'parents/') );
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
        $like = new \App\Models\Like();
        $testTeams = $team->where('name','like','test%')->get();
        foreach ($testTeams as $testTeam) {
            $testMembers = $member->where('parent_type','=','teams')
            ->where('parent','=',$testTeam->id)->get();
            foreach ($testMembers as $testMember) {
                $like->where('parent_type','=','members')
                ->where('parent','=',$testMember->id)
                ->delete();
                $member->where('id','=',$testMember->id)->delete();
            }
            $like->where('parent_type','=','teams')
            ->where('parent','=',$testTeam->id)
            ->delete();
            $team->where('id','=',$testTeam->id)->delete();
        }
        
        $testUsers = $user->where('name','like','test%')->get();
        foreach ($testUsers as $testUser) {
            $like->where('user_id','=',$testUser->id)->delete();
            $user->where('id','=',$testUser->id)->delete();
        }
        $this->assertEquals(1,1);
    }
}
