<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Team;
use App\Http\Models\Project;
use App\Http\Controllers\ProjectController;
use GrahamCampbell\ResultType\Result;

define('UNITTEST','1');

class ProjectTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    public $testProject1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new ProjectController();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $member = new \App\Models\Member();
        $team = new \App\Models\Team();
        $project = new \App\Models\Project();
        
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
        $project->where('name','=','testProject')->delete();
        
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
            "config" => '{"projectActivate": 10 }',
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
    
    public function test_create_notLogged() {
        $team = new \App\Models\Team();
        $projectModel = new \App\Models\Project();
        \Auth::logout();
		$team = $team->where('name','=','testTeam1')->first(); 
        $redirect = $this->controller->create($team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    
    public function test_create_logged_notMember() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = $team->where('name','=','testTeam1')->first(); 
        $redirect = $this->controller->create($team);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    public function test_create_logged_member() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = $team->where('name','=','testTeam1')->first(); 
        $view = $this->controller->create($team);
        $this->assertEquals( 'project.form', $view->getName());
    }

    public function test_store_notLogged() {
        $team = new \App\Models\Team();
        \Auth::logout();
		$team = $team->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "team_id" => $team->id,
            "name" => 'testProject',
            "description" => "",
			"ranks" => "admin",
			"deadline" => "2022-01-01"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_notMember() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = $team->where('name','=','testTeam1')->first(); 
        $request = new Request(["id" => 0, "team_id" => $team->id,
            "name" => 'testProject',
            "description" => "",
			"ranks" => "admin",
			"deadline" => "2022-01-01"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_member() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = $team->where('name','=','testTeam1')->first(); 
        $request = new Request(["team_id" => $team->id,
            "name" => "testProject",
            "description" => "",
			"ranks" => "admin,president",
			"deadline" => "2022-12-31"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );

		$project = \DB::table('projects')
		->where('name','=','testProject')
		->first();
		$this->assertEquals('testProject',$project->name);
		$this->assertEquals($team->id,$project->team_id);
    }

    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $project = new \App\Models\Project();
        $project = $project->where('name','=','testProject')->first();
        $view = $this->controller->edit($project);
        $this->assertEquals('project.form', $view->getName());
    }
    
    public function test_edit_loggedNotAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $project = new \App\Models\Project();
        $project = $project->where('name','=','testProject')->first();
        $redirect = $this->controller->edit($project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        $project = new \App\Models\Project();
        $project = $project->where('name','=','testProject')->first();
        $redirect = $this->controller->edit($project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
        
    public function test_update_notLogged() {
        \Auth::logout();
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $request = new Request([
            "name" => 'testProject javitva'
        ]);
        $redirect = $this->controller->update($request, $project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $request = new Request([
            "name" => 'testProject javitva'
        ]);
        $redirect = $this->controller->update($request,$project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
		$team = \DB::table('teams')->where('name','=','testTeam1')->first(); 
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $request = new Request([
            "name" => 'testProject javitva'
        ]);
        $redirect = $this->controller->update($request, $project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
        $option = \DB::table('projects')->where('name','=','testProject javitva')->first();
        $this->assertEquals('testProject javitva',$option->name);
    }
    
    
    public function test_end() {
        // test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $project = new \App\Models\Project();
        $option = new \App\Models\Option();
        $team->where('name','like','test%')->delete();
        $project->where('name','=','testProject')->delete();
        $project->where('name','=','testProject javitva')->delete();
        $testUser1 = $user->where('name','=','testUser1')->first();
        $member->where('user_id','=',$testUser1->id)->delete();
        $testUser2 = $user->where('name','=','testUser2')->first();
        $member->where('user_id','=',$testUser2->id)->delete();
        $user->where('name','like','test%')->delete();
        $this->assertEquals(1,1);
    }
}
