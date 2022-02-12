<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\Member;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)   {
		$data = Project::getData($team->id,8);
        if (count($data) > 0) {   			 
    		$info = Project::getInfo($data[0]);
    	} else {
			$info = Project::getInfoFromTeam($team);    	
    	}	
    	\Request::session()->put('projectsListUrl',\URL::current());
        return view('project.index',
        	["data" => $data,
        	"team" => $team,
        	"info" => $info,
        	"user" => false
        	])
        ->with('i', (request()->input('page', 1) - 1) * 8);
    }

    /**
     * Display a listing of the resource.
     * projektek amiknek az adott user tagja
     * @return \Illuminate\Http\Response
     */
    public function listByUser(int $userId)   {
		$data = Project::getDataByUser($userId,8);
        if (count($data) > 0) {   			 
    		$info = Project::getInfo($data[0]);
    	} else {
			$info = Project::getInfo(Project::emptyRecord());    	
    	}	
    	$info->userMember = true;
    	$info->userAdmin = false;
    	$user = \DB::table('users')->where('id','=',$userId)->first();
    	\Request::session()->put('projectsListUrl',\URL::current());
        return view('project.index',
        	["data" => $data,
        	"team" => false,
        	"user" => $user,
        	"info" => $info
        	])
        ->with('i', (request()->input('page', 1) - 1) * 8);
    }
    

	 protected function userMember(array $userRank): bool {
	 	return (in_array('active_member',$userRank) | 
	 	        in_array('active_admin',$userRank));
	 }	

	 protected function userAdmin(array $userRank): bool {
	 	return in_array('active_admin',$userRank);
	 }	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Team $team)
    {
    	
    	$project = Project::emptyRecord();
    	$project->team_id = $team->id;	
    	$info = Project::getInfoFromTeam($team);
    	if (!$this->accessCheck('add', $info)){
    		   return redirect(\URL::to('/'.$team->id.'/projects'))
    		   			->with('error',__('project.accessDenied')); 	
   		}

        return view('project.form',
        ["project" => $project,
          "team" => $team,
         "info" => $info]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     *         ['id','parent', 'name','description','avatar','deadline', 'config']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    		// jogosultság ellenörzés
    		$team = \DB::table('teams')
    		->where('id','=',$request->input('team_id','0'))
    		->first();
    		if (!$team) {
				echo 'Fatal error team not exists'; exit();    		
    		}
    		$info = Project::getInfoFromTeam($team);
			if (!$this->accessCheck('add', $info)) {
    		   return redirect()->to('/'.$team->id.'/projects')
   		   						->with('error',__('project.accessDenied')); 	
    		}

			Project::valid($request);
				
			// project rekord kiirása
			$id = 0;
			$errorInfo = Project::saveOrStore($id,$request);
			
			// a létrehozó (bejelentkezett) user "admin" tagja a csoportnak
			// members rekord tárolás az adatbázisba
			if ($errorInfo == '') {
				$errorInfo = Project::addAdmin($id);
			}    

		   // result kialakitása			
			if ($errorInfo == '') { 
    		   $result = redirect()->to('/'.$team->id.'/projects')
			                 ->with('success',__('project.successSave') );
			} else {
    		   $result = redirect()->to('/'.$team->id.'/projects')
    		   						->with('error',__('project.accessDenied')); 	
			}
			return $result;                 
    }
    
    /**
    * project->config json string dekodolása
    * @param Project $project
    * @return void
    */      
    
    protected function decodeConfig(Project &$project) {
    	  $project->config = JSON_decode($project->config);
    	  if (!isset($project->config->ranks)) {
    	  		$project->config->ranks = ['admin','manager','president','moderator'];
    	  } else if (is_string($project->config->ranks)) {
				$project->config->ranks = explode(',',$project->config->ranks);
    	  }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $team = \DB::table('teams')->where('id','=',$project->team_id)->first();  
    		if (!$team) {
				echo 'Fatal error team not exists'; exit();    		
    		}
    	  $info = Project::getInfo($project); 
		  $this->decodeConfig($project, $info);
     	  if ($info->parentClosed) {
				$project->status = 'closed';    	  
    	  }	
    	  $tasks = \DB::table('tasks')
    	  	->where('project_id','=',$project->id)
    	  	->orderBy('status','asc')
    	  	->orderBy('position','asc')
    	  	->get();
    	  foreach ($tasks as $task) {
				if ($task->assign != 0) {
					$u = \DB::table('users')
						->where('id','=',$task->assign)
						->first();
					if ($u) {
					    $task->assign = [$u->name, 
					        \App\Models\User::userAvatar($u->profile_photo_path, $u->email)
					    ];
					} else {
						$task->assign=['','']; 					
					}					
				} else {
					$task->assign = ['',''];				
				}    	  
    	  }	
        return view('project.show',
        	["project" => $project,
        	 "team" => $team,
        	 "info" => $info,
        	 "tasks" => $tasks
        	]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
    	  $team = \DB::table('teams')->where('id','=',$project->team_id)->first();
    	  if (!$team) {
				echo 'Fatal error team not found'; exit();    	  
    	  }	
    	  $info = Project::getInfo($project);
		  $this->decodeConfig($project, $info);
    	  if ($info->parentClosed) {
				$project->status = 'closed';    	  
    	  }	

		  if (!$this->accessCheck('edit', $info, $project)) {
    		   return redirect()->to('/'.$team->id.'/projects')
    		   						->with('error',__('project.accessDenied')); 	
    	  } 	

        return view('project.form',
        	["project" => $project,
        	 "team" => $team,
          "info" => $info
        	]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
   	  $team = \DB::table('teams')->where('id','=',$project->team_id)->first();
    	  if (!$team) {
				echo 'Fatal error team not found'; exit();    	  
    	  }	
    	// jogosultság ellenörzés	
    	$info = Project::getInfo($project);
	    if (!$this->accessCheck('edit', $info, $project)) {
    	   return redirect()->to(\URL::to('/'.$team->id.'/projects'))
    		   						->with('error',__('project.accessDenied')); 	
    	} 	
    	  
		Project::valid($request);
		
		// project rekord kiirása
		$id = $project->id;
		$errorInfo = Project::saveOrStore($id, $request);
		
		// result kialakítása		
		if ($errorInfo == '') {
    		 $result = redirect()->to(\URL::to('/'.$team->id.'/projects'))
                      ->with('success',__('project.successSave'));
		} else {
    		 $result = redirect()->to(\URL::to('/'.$team->id.'/projects'))
    		   						->with('error',__('project.accessDenied')); 	
		}
		return $result;                        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
    }
    
   	protected function accessCheck(string $action, $info, $project = false) {		  
		$result = false;
    	if ($action == 'add') {
    		$result = ((\Auth::check()) & 
    		    (count($info->userParentRank) > 0) & 
    		    (!$info->parentClosed));
		}
		if ($action == 'edit') {
    	  $result =  ((\Auth::check()) &
    	      ($this->userAdmin($info->userRank)) &
    	      (!$info->parentClosed) &
    	      ($project->status != 'closed')); 
		}		  
		return $result;			
	}				

}


