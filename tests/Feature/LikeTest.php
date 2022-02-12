<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Models\User;
use App\Http\Models\Team;
use App\Http\Models\Like;
use App\Http\Controllers\LikeController;
use GrahamCampbell\ResultType\Result;

class LikeTest extends TestCase
{
    public $controller;
    public $testUser1;
    public $testTeam1;
    
    function __construct() {
        parent::__construct();
        $this->controller = new LikeController();
    }
    
    public function test_start()  {
        // esetleg meglévő korábbi test adatok törlése
        $this->test_end();
        
        // test userek létrehozása
        $user = new \App\Models\User();
        $team = new \App\Models\Team();
        
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
   
	 public function test_like_not_logged() {
        \Auth::logout();
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$this->controller->like('teams',$testTeam1->id);
         $model = new \App\Models\Like();
			$like = $model->where('parent_type','=','teams')
								->where('parent','='.$testTeam1->id)->first();
        	$this->assertNull($like);
	 }	    
	 public function test_dislike_not_logged() {
        \Auth::logout();
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$this->controller->dislike('teams',$testTeam1->id);
         $model = new \App\Models\Like();
			$like = $model->where('parent_type','=','teams')
								->where('parent','='.$testTeam1->id)->first();
        	$this->assertNull($like);
	 }	    
	 public function test_likeInfo_notlogged() {
        \Auth::logout();
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
			$view = $this->controller->likeInfo('teams',$testTeam1->id);
			$this->assertEquals('like.info',$view->getName());
	 }	 
	 public function test_like_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$this->controller->like('teams',$testTeam1->id);
         $model = new \App\Models\Like();
			$like = $model->where('parent_type','=','teams')
								->where('parent','=',$testTeam1->id)->first();
        	$this->assertEquals($like->parent, $testTeam1->id);
	 }	    
	 public function test_dislike_logged() {
        $user = \App\Models\User::where('name','=','testUser1')->first();
        \Auth::loginUsingId($user->id, TRUE);
	 		$teamModel = new \App\Models\Team(); 
	 		$testTeam1 = $teamModel->where('name','=','testTeam1')->first();
	 		$request = new \Illuminate\Http\Request();
			$this->controller->dislike('teams',$testTeam1->id);
         $model = new \App\Models\Like();
			$likeCount = $model->where('parent_type','=','teams')
			->where('parent','=',$testTeam1->id)->count();
        	$this->assertEquals($likeCount, 2);
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
