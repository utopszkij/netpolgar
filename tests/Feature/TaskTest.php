<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Team;
use App\Http\Models\Project;
use App\Http\Models\Task;
use App\Http\Controllers\TaskController;
use GrahamCampbell\ResultType\Result;

if (!defined('UNITTEST')) {
	define('UNITTEST','1');
}

class TaskTest extends TestCase {
    public $controller;
    public $testUser1;
    public $testTeam1;
    public $testProject1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new TaskController();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $member = new \App\Models\Member();
        $team = new \App\Models\Team();
        $project = new \App\Models\Project();
        $task = new \App\Models\Task();
        
        // esetleg meglévő korábbi test adatok törlése
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
        // testProject létrehozása
        $project0 = \App\Models\Project::emptyRecord();
        $testProject = $project->create([
        "team_id" => $testTeam1->id,
        "name" => "testProject",
        "description" => "testProject description",
        "deadline" => "2022-12-31",
        "config" => JSON_encode($project0->config),
        "created_by" => $testUser1->id
        ]);
        // testUser1 adminja a testProject -nek
        \App\Models\Member::create([
            'parent_type' => 'projects',
            'parent' => $testProject->id,
            'user_id'=> $testUser1->id,
            'rank' => 'admin',
            'status' => 'active',
            'created_by' => $testUser1->id
        ]);
        $this->assertEquals(1,1);
        
    }

    public function test_create_notLogged() {
        $team = new \App\Models\Team();
        \Auth::logout();
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $redirect = $this->controller->create($project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    
    public function test_create_logged_notMember() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $redirect = $this->controller->create($project);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
    public function test_create_logged_member() {
        $team = new \App\Models\Team();
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $view = $this->controller->create($project);
        $this->assertEquals( 'task.form', $view->getName());
    }

    public function test_store_notLogged() {
        \Auth::logout();
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $request = new Request(["id" => 0, "project_id" => $project->id,
            "name" => 'testProject',
            "deadline" => "2022-12-31",
			"type" => "bug"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $projectModel = new \App\Models\Project();
		$project = $projectModel->where('name','=','testProject')->first(); 
        $request = new Request(["id" => 0, "project_id" => $project->id,
            "name" => 'testProject',
            "deadline" => "2022-12-31",
			"type" => "bug"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_store_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $projectModel = new \App\Models\Project();
        $project = $projectModel->where('name','=','testProject')->first();
        $request = new Request(["id" => 0, "project_id" => $project->id,
            "name" => 'testTask',
            "deadline" => "2022-12-31",
			"type" => "bug"
        ]);
        $redirect = $this->controller->store($request);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );

		$task = \DB::table('tasks')
		->where('name','=','testTask')
		->first();
		$this->assertEquals('testTask',$task->name);
    }

    public function test_edit_loggedAdmin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $taskModel = new \App\Models\Task();
		$task = $taskModel->where('name','=','testTask')->first(); 
        $view = $this->controller->edit($task);
        $this->assertEquals('task.form', $view->getName());
    }
    
    public function test_edit_notLogged() {
        \Auth::logout();
        $taskModel = new \App\Models\Task();
		$task = $taskModel->where('name','=','testTask')->first(); 
        $redirect = $this->controller->edit($task);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }
        
    public function test_update_notLogged() {
        \Auth::logout();
        $taskModel = new \App\Models\Task();
		$task = $taskModel->where('name','=','testTask')->first(); 
        $request = new Request([
            "name" => 'testTaskt javitva'
        ]);
        $redirect = $this->controller->update($request, $task);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_notMember() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $taskModel = new \App\Models\Task();
		$task = $taskModel->where('name','=','testTask')->first(); 
        $request = new Request([
            "name" => 'testTask javitva'
        ]);
        $redirect = $this->controller->update($request,$task);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
    }

    public function test_update_logged_member() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $taskModel = new \App\Models\Task();
        $projectModel = new \App\Models\Project();
        $task = $taskModel->where('name','=','testTask')->first(); 
		$testProject = $projectModel->where('name','=','testProject')->first();
		$request = new Request([
            "project_id" => $testProject->id,
            "name" => 'testTask javitva',
            "type" => '',
            "status" => 'active',
            "deadline" => '2022-12-31'
        ]);
        $redirect = $this->controller->update($request, $task);
        $this->assertGreaterThan( 0, strpos($redirect->content(),'/') );
        $task = \DB::table('tasks')->where('name','=','testTask javitva')->first();
        $this->assertEquals('testTask javitva',$task->name);
    }
    
    public function test_end() {
        // test adatok törlése
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $project = new \App\Models\Project();
        $task = new \App\Models\Task();
        $testUser1 = $user->where('name','=','testUser1')->first();
        if ($testUser1) {
            $member->where('user_id','=',$testUser1->id)->delete();
            $member->where('created_by','=',$testUser1->id)->delete();
            $task->where('assign','=',$testUser1->id)->delete();
        }	
        
        $task->where('name','=','testTask')->delete();
        $task->where('name','=','testTask javitva')->delete();
        
        $project->where('name','=','testProject')->delete();
        $project->where('name','=','testProject javitva')->delete();
        
        $team->where('name','like','test%')->delete();
        
        $user->where('name','=','testUser1')->delete();
        $user->where('name','=','testUser2')->delete();
        $this->assertEquals(1,1);
    }
}
