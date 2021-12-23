<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\Rules\RanksRule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $data = \DB::table('projects')
        			 ->where('team_id','=',$team->id)
        			 ->orderBy('name')
        			 ->paginate(5);
      if (count($data) > 0) {   			 
    		$info = Project::getInfo($data[0]);
    	} else {
			$info = Project::getInfoFromTeam($team);    	
    	}	
      return view('project.index',
        	["data" => $data,
        	"team" => $team,
        	"info" => $info])
         ->with('i', (request()->input('page', 1) - 1) * 5);
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
    		// csak parent csoport tag vihet fel
    		if ((!\Auth::user()) | 
    		    (count($info->userParentRank) == 0) | 
    		    ($info->parentClosed)) {
    		   return redirect()->to('/'.$team->id.'/projects')
    		   						->with('error',__('project.accessDenied')); 	
    		}

        return view('project.form',
        ["project" => $project,
          "team" => $team,
         "info" => $info]);
    }

	 /**
	 * project rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 protected Function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			$projectArr = [];
			$projectArr['team_id'] = $request->input('team_id');
			$projectArr['name'] = $request->input('name');
			$projectArr['description'] = $request->input('description');
			$projectArr['avatar'] = $request->input('avatar');
			$projectArr['deadline'] = $request->input('deadline');
			if ($id == 0) {
				$projectArr['status'] = 'proposal';
				if (\Auth::user()) {
					$projectArr['created_by'] = \Auth::user()->id;
				} else {
					$projectArr['created_by'] = 0;
				}		
			}
			   
			// config kialakitása
			$config = new \stdClass();
			$config->ranks = explode(',',$request->input('ranks'));
			$config->close = $request->input('close');
			$config->memberActivate = $request->input('memberActivate');
			$config->memberExclude = $request->input('memberExclude');
			$config->rankActivate = $request->input('rankActivate');
			$config->rankClose = $request->input('rankClose');
			$config->debateActivate = $request->input('debateActivate');
			$projectArr['config'] = JSON_encode($config);

			// project rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
				$model = new Project();
				if ($id == 0) {
			 		$projectRec = $model->create($projectArr);
			 		$id = $projectRec->id;
			 	} else {
					$model->where('id','=',$id)->update($projectArr);			 	
			 	}	
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = $exception->errorInfo;
			}	
			return $errorInfo;		
	 }	

	 /**
	 * bejelentkezett user legyen admin -ja az $id project -nek
	 * @param int $id
	 * @return string
	 */
    protected function addAdmin(int $id): string {		
				$memberArr = [];
				$memberArr['parent_type'] = 'projects';
				$memberArr['parent'] = $id;
				$memberArr['user_id'] = \Auth::user()->id;
				$memberArr['rank'] = 'admin';
				$memberArr['status'] = 'active';
				$memberArr['created_by'] = \Auth::user()->id;
				$errorInfo = '';
				try {
					Member::create($memberArr);
				} catch (\Illuminate\Database\QueryException $exception) {
			     $errorInfo = $exception->errorInfo;
				}
				return $errorInfo;			
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
    		if ((!\Auth::user()) | 
    		    (count($info->userParentRank) == 0) |
    		    ($info->parentClosed)) {
    		   return redirect()->to('/'.$team->id.'/projects')
    		   						->with('error',__('project.accessDenied')); 	
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'ranks' => ['required', new RanksRule()],
				'description' => 'required',
				'deadline' => 'required'
			]);

			// project rekord kiirása
			$id = 0;
			$errorInfo = $this->saveOrStore($id,$request);
			
			// a létrehozó (bejelentkezett) user "admin" tagja a csoportnak
			// members rekord tárolás az adatbázisba
			if ($errorInfo == '') {
				$errorInfo = $this->addAdmin($id);
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
        return view('project.show',
        	["project" => $project,
        	 "team" => $team,
        	 "info" => $info
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

		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      (!$this->userAdmin($info->userRank)) |
    	      ($info->parentClosed) |
    	      ($project->status == 'closed')) {
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
    	if ((!\Auth::user()) | 
    	    (!$this->userAdmin($info->userRank)) |
    	    ($info->parentClosed) |
    	    ($request->input('status') == 'closed')) {
    		   return redirect()->to('/'.$team->id.'/projects')
    		   						->with('error',__('project.accessDenied')); 	
    	} 	
    	  
		// tartalmi ellenörzés      
        $request->validate([
            'name' => 'required',
				'ranks' => ['required', new RanksRule()],
            'description' => 'required',
            'deadline' => 'required'
      ]);

		// project rekord kiirása
		$id = $project->id;
		$errorInfo = $this->saveOrStore($id, $request);
		
		// result kialakítása		
		if ($errorInfo == '') {
    		 $result = redirect()->to('/'.$team->id.'/projects')
                      ->with('success',__('project.successSave'));
		} else {
    		 $result = redirect()->to('/'.$team->id.'/projects')
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
}


