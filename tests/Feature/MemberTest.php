<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\Team;
use App\Http\Controllers\TeamController;
use GrahamCampbell\ResultType\Result;

class MemberTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new \App\Http\Controllers\MemberController();
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
            "config" => "{\"ranks\":[\"admin\",	\"manager\"],
            	\"memberActivate\":10,
            	\"memberExclude\":10,
            	\"rankActivate\":10,
            	\"rankClose\":10
            }",
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
        \App\Models\Member::create([
            'parent_type' => 'teams',
            'parent' => $testTeam1->id,
            'user_id'=> $testUser1->id,
            'rank' => 'member',
            'status' => 'active',
            'created_by' => $testUser1->id
        ]);
        $this->assertEquals(1,1);
    }
    
    public function test_index_notLogged() {
        \Auth::logout();
        $team = new \App\Models\Team();
        $testTeam1 = $team->where('name','=','testTeam1')->first();
        $view = $this->controller->index('teams', $testTeam1->id);
        $viewData = $view->getData();
        // ['data'] paginator Result   -- total(),  --  items() /from current page/
        // ['parent'] string
        // ['info'] object
        $viewName = $view->getName();
        $this->assertEquals( 'member.index', $viewName);
        $this->assertGreaterThan( 0, $viewData['data']->total());
    }
    
    public function test_show_found() {
        $team = new \App\Models\Team();
        $testTeam1 = $team->where('name','=','testTeam1')->first();
        $member = new \App\Models\Member();
        $testMember1 = $member->where('parent','=',$testTeam1->id)
                              ->where('parent_type','=','teams')->first();
        
        $view = $this->controller->show($testMember1->id);
        $this->assertEquals('member.show', $view->getName());
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
