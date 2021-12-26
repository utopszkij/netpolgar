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
    public function create(Project $project)
    {
    	
    	  $task = Task::emptyRecord();
    	  $task->project_id = $project->id;	
    	  $info = Project::getInfo($project);
    	  $taskInfo = Task::getInfo(false);
    		// csak project admin vihet fel
    		if ((!\Auth::user()) | 
    		    (!$info->userAdmin)) {  
    		    
    		   return redirect()->to('/projects'.$project->id)
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
          "members" => $members]);
    }

	 /**
	 * project rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 protected Function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			$taskArr = [];
			$taskArr['project_id'] = $request->input('project_id');
			$taskArr['name'] = $request->input('name');
			$taskArr['deadline'] = $request->input('deadline');
			$taskArr['type'] = $request->input('type');
			$taskArr['status'] = $request->input('status');
			$taskArr['assign'] = $request->input('assign');
			if ($id == 0) {
				if (\Auth::user()) {
					$taskArr['created_by'] = \Auth::user()->id;
				} else {
					$taskArr['created_by'] = 0;
				}		
			}

			// task rekord tárolás az adatbázisba
			try {
				$model = new Task();
				if ($id == 0) {
			 		$taskRec = $model->create($taskArr);
			 		$id = $taskRec->id;
			 	} else {
					$model->where('id','=',$id)->update($taskArr);			 	
			 	}	
				$errorInfo = '';
			} catch (\Illuminate\Database\QueryException $exception) {
		      $errorInfo = $exception->getMessage();
			}	
			return $errorInfo;		
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
    		// jogosultság ellenörzés
    		$project = \DB::table('projects')
    		->where('id','=',$request->input('project_id','0'))
    		->first();
    		if (!$project) {
				echo 'Fatal error project not exists'; exit();    		
    		}
    		$info = Project::getInfo($project);
    		if ((!\Auth::user()) | 
    		    (!$info->userAdmin)) { 
    		   return redirect()->to('/projects/'.$project->id)
    		   						->with('error',__('task.accessDenied')); 	
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'type' => 'required',
				'status' => 'required',
				'deadline' => 'required'
			]);

			// task rekord kiirása
			$id = 0;
			$errorInfo = $this->saveOrStore($id,$request);
			
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
		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      ((!$info->userAdmin) & (!$info->userMember))) { 
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
    	  if ((!\Auth::user()) |
    	      ((!$info->userAdmin) & (!$info->userMember))) { 
    		   return redirect()->to('/projects/'.$project->id)
    		   						->with('error',__('task.accessDenied')); 	
    	  } 	
    	  
		// tartalmi ellenörzés      
        $request->validate([
            'name' => 'required',
				'status' => 'required',
            'type' => 'required',
            'deadline' => 'required'
      ]);

		// admin bármit modosithat, a többi tag csak 
		// - magához rendelhet eddig szabad taskot
		// - hozzá rendelt taskot modosithat
		// - hozzá rendeltet felszabadithat
		$user = \Auth::user();      
		$id = $task->id;
		if ($info->userAdmin) {
			$errorInfo = $this->saveOrStore($id, $request);
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

		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      (!$info->userAdmin)) { 
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
}


