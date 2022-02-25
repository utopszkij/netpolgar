<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Event;
use App\Http\Controllers\EventController;
use GrahamCampbell\ResultType\Result;

if (!defined('UNITTEST')) {
	define('UNITTEST','1');
}

class EventTest extends TestCase {
    
    protected $controller;
    protected $model;

    function __construct() {
        parent::__construct();
        $this->controller = new EventController();
        $this->model = new \App\Models\Event();
    }
    
    public function test_start()  {
        $user = new \App\Models\User();
        $member = new \App\Models\Member();
        $team = new \App\Models\Team();
        
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
        $this->assertEquals(1,1);
    }
    
    public function test_index() {
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $view = $this->controller->index('teams', $testTeam->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $viewName = $view->getName();
        $this->assertEquals( 'event.index', $viewName);
    }
    
    public function test_create_not_logged() {
        \Auth::logout();
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $redirect = $this->controller->create('teams', $testTeam->id);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_create_logged_notAdmin() {
        $user = \App\Models\User::where('name','=','testUser2')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $redirect = $this->controller->create('teams', $testTeam->id);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_create_logged_admin() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $view = $this->controller->create('teams', $testTeam->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $viewName = $view->getName();
        $this->assertEquals( 'event.form', $viewName);
    }
    
    public function test_store_not_logged() {
        \Auth::logout();
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $testUser1 = \DB::table('users')->where('name','=','testUser1')->first();
        $request = new Request([
            "parent_type" => "teams",
            "parent" => $testTeam->id,
            "name" => "testEvent",
            "description" => "123",
            "avatar" => "",
            "location" => "Budapest III.",
            "datae" => "2022-01-01",
            "hours" => 8,
            "minute" => 0,
            "length" => 20,
            "created_by" => $testUser1->id
        ]);
        $redirect = $this->controller->store($request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_store_logged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($testUser1->id, TRUE);
        $testTeam = \DB::table('teams')->where('name','=','testTeam1')->first();
        $request = new Request(["id" => 0,
            "parent_type" => "teams",
            "parent" => $testTeam->id,
            "name" => "testEvent",
            "description" => "123",
            "avatar" => "",
            "location" => "Budapest III.",
            "date" => "2022-01-01",
            "hours" => 8,
            "minutes" => 0,
            "length" => "20 perc",
            "created_by" => $testUser1->id
        ]);
        
        $redirect = $this->controller->store($request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
 
    public function test_show_notfound() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $redirect = $this->controller->show(0);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_show_found() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $view = $this->controller->show($testEvent->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $viewName = $view->getName();
        $this->assertEquals( 'event.show', $viewName);
        
    }
    
    public function test_edit_notfound() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
        $redirect = $this->controller->edit(0);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_edit_notLogged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        \Auth::logout();
        $view = $this->controller->edit($testEvent->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $viewName = $view->getName();
        $this->assertEquals( 'event.show', $viewName);
        
    }
    
    public function test_edit_found() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        \Auth::logout();
        $view = $this->controller->edit($testEvent->id);
        $this->assertEquals('Illuminate\View\View', get_class($view));
        $viewName = $view->getName();
        $this->assertEquals( 'event.show', $viewName);
        
    }

    public function test_update_notlogged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::logout();
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->update($testEvent->id,$request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_update_logged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($testUser1->id, TRUE);
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->update($testEvent->id,$request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_subscription_logged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($testUser1->id, TRUE);
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->subscription($testEvent->id,$request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_subscription_notlogged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::logout();
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->subscription($testEvent->id,$request);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_delete_notlogged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($testUser1->id, TRUE);
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->delete($testEvent->id);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_delete_logged() {
        $testUser1 = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($testUser1->id, TRUE);
        $testEvent = \DB::table('events')->where('name','=','testEvent')->first();
        $request = new Request([
            "description" => "123456",
        ]);
        $redirect = $this->controller->delete($testEvent->id);
        $this->assertEquals('Illuminate\Http\RedirectResponse', get_class($redirect));
    }
    
    public function test_end() {
        // test adatok törlése
        $this->model->where('name','=','testFile')->delete();
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        $member = new \App\Models\Member();
        $event = new \App\Models\Event();
        
        $testUser1 = $user->where('name','=','testUser1')->first();
        if ($testUser1) {
            $member->where('user_id','=',$testUser1->id)->delete();
            $member->where('created_by','=',$testUser1->id)->delete();
        }
        $testUser2 = $user->where('name','=','testUser1')->first();
        if ($testUser2) {
            $member->where('user_id','=',$testUser1->id)->delete();
            $member->where('created_by','=',$testUser1->id)->delete();
        }
        $event->where('name','=','testFile')->delete();
        $team->where('name','like','test%')->delete();
        $user->where('name','=','testUser1')->delete();
        $user->where('name','=','testUser2')->delete();
        $this->assertEquals(1,1);
    }
}
