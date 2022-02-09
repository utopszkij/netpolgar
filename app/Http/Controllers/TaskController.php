<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class TaskController extends Controller {

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project) {
    	
    	  $task = Task::emptyRecord();
    	  $task->project_id = $project->id;	
    	  $info = Project::getInfo($project);
    	  $taskInfo = Task::getInfo(false);
    	  
    	  if (!$this->accessCheck('add', $info)) {
    		   return redirect(\URL::to('/projects/'.$project->id))
    		   						->with('error',__('task.accessDenied')); 	
   		  }
    	  $members = Task::getMembers($project->id);
          return view('task.form',
          ["project" => $project,
          "task" => $task,
          "info" => $info,
          "taskInfo" => $taskInfo,
          "members" => $members]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     *         ['id','project_id', 'name','status','type', 'assign',
     *          'avatar','deadline']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    		$project = \DB::table('projects')
    		->where('id','=',$request->input('project_id','0'))
    		->first();
    		if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    		}
    		
    		$info = Project::getInfo($project);
    	    if (!$this->accessCheck('add', $info)) {
    		   return redirect()->to('/projects/'.$project->id)
    		   						->with('error',__('task.accessDenied')); 	
    		}

			Task::valid($request);

			// task rekord kiirása
			$id = 0;
			$errorInfo = Task::saveOrStore($id,$request);
			
		   // result kialakitása			
			if ($errorInfo == '') { 
    		   $result = redirect()->to('/projects/'.$project->id)
			                 ->with('success',__('task.successSave') );
			} else {
    		   $result = redirect()->to('/projects/'.$project->id)
    		   						->with('error',$errorInfo); 	
			}
			return $result;                 
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $project = \DB::table('projects')
        	->where('id','=',$task->project_id)
        	->first();  
    	if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    	}
    	$info = Project::getInfo($project);
    	$taskInfo = Task::getInfo($task); 
		$assignUser = \DB::table('users')
				->where('id','=',$task->assign)
				->first();
		if ($assignUser) {
				$task->assign = $assignUser->name;		  
		}		
        return view('task.show',
        	["project" => $project,
        	 "task" => $task,
        	 "info" => $info,
        	 "taskInfo" => $taskInfo
        	]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $project = \DB::table('projects')
        	->where('id','=',$task->project_id)
        	->first();  
    	if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    	}
    	$info = Project::getInfo($project); 
		$taskInfo = Task::getInfo($task);	
    	if (!$this->accessCheck('edit', $info)) {
    		   return redirect()->to('/projects/'.$project->id)
    		   					->with('error',__('task.accessDenied')); 	
    	} 	
		$members = \DB::table('members')
				->select('members.user_id', 'users.name', 'users.profile_photo_path')
				->leftJoin('users','users.id','members.user_id')
				->where('members.parent_type','=','projects')
				->where('members.parent','=',$project->id)
				->where('members.status','=','active')
				->distinct()
				->get();

        return view('task.form',
        	["project" => $project,
        	 "task" => $task,
          "info" => $info,
          "taskInfo" => $taskInfo,
          "members" => $members
        	]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $project = \DB::table('projects')
        	->where('id','=',$task->project_id)
        	->first();  
    		if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    	}
    	$info = Project::getInfo($project); 

		// Jogosultság ellenörzés
    	if (!$this->accessCheck('edit', $info)) {
    		   return redirect()->to('/projects/'.$project->id)
    		   						->with('error',__('task.accessDenied')); 	
    	} 	
    	  
		// tartalmi ellenörzés      
		Task::valid($request);

		// admin bármit modosithat, a többi tag csak 
		// - magához rendelhet eddig szabad taskot
		// - hozzá rendelt taskot modosithat
		// - hozzá rendeltet felszabadithat
		$user = \Auth::user();      
		$id = $task->id;
		if ($info->userAdmin) {
			$errorInfo = Task::saveOrStore($id, $request);
		} else {
			
			if (($task->assign == 0) & ($request->assign == $user->id)) {
				// most veszi magához
				$errorInfo = $this->saveOrStore($id, $request);
			} else if (($task->assign == $user->id) & ($request->assign == $user->id)) {
				// eddig is nála volt, nála is marad
				$errorInfo = $this->saveOrStore($id, $request);
			} else if (($task->assign == $user->id) & ($request->assign == 0)) {
				// eddig nála volt, elengedi
				$errorInfo = $this->saveOrStore($id, $request);
			} else {
    		   $errorInfo = __('task.accessDenied').'(1)'; 	
			}
		}
		
		// result kialakítása		
		if ($errorInfo == '') {
    		 $result = redirect()->to('/projects/'.$project->id)
                      ->with('success',__('task.successSave'));
		} else {
    		 $result = redirect()->to('/projects/'.$project->id)
    		   						->with('error',$errorInfo); 	
		}
		return $result;                        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)  {
        $project = \DB::table('projects')
        	->where('id','=',$task->project_id)
        	->first();  
    		if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    	}
    	$info = Project::getInfo($project); 

    	if (!$this->accessCheck('delete', $info)) {
    		   return redirect()->to('/projects/'.$project->id)
    		   						->with('error',__('task.accessDenied')); 	
    	} 	
			
		// törlés
		$model = new Task();
		$model->where('id','=',$task->id)
		  			->delete();
    	return redirect()->to('/projects/'.$project->id);
    }
    
	 /**
	 * huzd és dobd modon mozgatás után tárolás
	 * @param Request data='id,status,pozicio,....'
	 */
    public function dragsave(Request $request) {
    	$s = $request->input('data');
    	$w = explode(',',$s);
    	$i = 0;
    	while ($i < (count($w) - 2)) {
			$id = $w[$i];
			$status = $w[$i+1];
			$position = $w[$i+2];
			\DB::table('tasks')
			->where('id','=',$id)
			->update(["status" => $status, "position" => $position]);
			$i = $i + 3;    	
    	} 
    	echo "saved";
    }
    
    protected function accessCheck(string $action, $info): bool {			  
		$result = false;
    	if ($action == 'add') {	
    		$result = ((\Auth::check()) & 
					   ($info->userAdmin));   
		}	
    	if ($action == 'edit') {	
    	  $result = ((\Auth::check()) &
    	             (($info->userAdmin) | (!$info->userMember)));  
		}		
    	if ($action == 'delete') {	
    	  $result = ((\Auth::check()) &
    	             ($info->userAdmin));  
		}		
		return $result;
    }					

}


