<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Rules\RanksRule;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $parent = '0')
    {
        $data = Team::latest()
        			 ->where('parent','=',$parent)
        			 ->orderBy('name')
        			 ->paginate(5);
    	  $info = Team::getInfo((int)$parent);
        return view('team.index',
        	["data" => $data,
        	"parent" => $parent,
        	"info" => $info])
         ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(string $parent = '0')
    {
    	
    	  $team = Team::emptyRecord();
    	  $team->parent = $parent;	
    	  $info = Team::getInfo($parent);

    		// csak csoport tag vihet fel
    		if ((!\Auth::user()) | 
    		    (count($info->userRank) == 0) | 
    		    ($info->parentClosed)) {
				return redirect()->route('parents.teams.index', ['parent' => $parent])
			                 ->with('error',__('team.accessDenied'));
    		}

        return view('team.form',
        ["team" => $team,
         "info" => $info]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     *         ['id','parent', 'name','description','avatar','config']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    		// jogosultság ellenörzés
    		$info = Team::getInfo($request->input('parent'));
    		if ((!\Auth::user()) | 
    		    (count($info->userRank) == 0) |
    		    ($info->status != 'active') |
    		    ($info->parentClosed)) {
				return redirect()->route('parents.teams.index', ['parent' => $teamArr['parent']])
			                 ->with('error',__('team.accessDenied'));
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'ranks' => ['required', new RanksRule()],
				'description' => 'required'
			]);
	      // ranks -ban admin kötelező!

			// rekord array kialakitása
			$teamArr = [];
			$teamArr['parent'] = $request->input('parent');
			$teamArr['name'] = $request->input('name');
			$teamArr['description'] = $request->input('description');
			$teamArr['avatar'] = $request->input('avatar');
			$teamArr['status'] = 'proposal';
			if (\Auth::user()) {
				$teamArr['created_by'] = \Auth::user()->id;
			} else {
				$teamArr['created_by'] = 0;
			}		
			   
			// config kialakitása
			$config = new \stdClass();
			$config->ranks = explode(',',$request->input('ranks'));
			$config->close = $request->input('close');
			$config->memberActivate = $request->input('memberActivate');
			$config->memberExclude = $request->input('memberExclude');
			$config->rankActivate = $request->input('rankActivate');
			$config->rankClose = $request->input('rankClose');
			$config->projectActivate = $request->input('projectActivate');
			$config->productActivate = $request->input('productActivate');
			$config->subTeamActivate = $request->input('subTeamActivate');
			$config->debateActivate = $request->input('debateActivate');
			$teamArr['config'] = JSON_encode($config);
			        

			// teams rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
			 	$teamRec = Team::create($teamArr);
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = $exception->errorInfo;
			}			
			
			// a létrehozó (bejelentkezett) user "admin" tagja a csoportnak
			// members rekord tárolás az adatbázisba
			if ($errorInfo == '') {
				$memberArr = [];
				$memberArr['parent_type'] = 'teams';
				$memberArr['parent'] = $teamRec->id;
				$memberArr['user_id'] = \Auth::user()->id;
				$memberArr['rank'] = 'admin';
				$memberArr['status'] = 'active';
				$memberArr['created_by'] = \Auth::user()->id;
				try {
					Member::create($memberArr);
				} catch (\Illuminate\Database\QueryException $exception) {
			     $errorInfo = $exception->errorInfo;
				}			
			}    

		   // result kialakitása			
			if ($errorInfo == '') { 
				$result = redirect()->route('parents.teams.index', ['parent' => $teamArr['parent']])
			                 ->with('success',__('team.successSave') );
			} else {
				$result = redirect()->route('parents.teams.index', ['parent' => $teamArr['parent']])
			                 ->with('error',$errorInfo);
			}
			return $result;                 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
    	  $info = Team::getInfo($team->id); 
    	  $team->config = JSON_decode($team->config);
    	  if (!isset($team->config->ranks)) {
    	  		$team->config->ranks = ['admin','manager','president','moderator'];
    	  }
    	  if ($info->parentClosed) {
				$team->status = 'closed';    	  
    	  }	
        return view('team.show',
        	["team" => $team,
        	 "info" => $info
        	]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
    	  $info = Team::getInfo($team->id);
    	  if ($info->parentClosed) {
				$team->status = 'closed';    	  
    	  }	
    	  if ((!\Auth::user()) |
    	      (!in_array('admin',$info->userRank)) |
    	      ($info->parentClosed) |
    	      ($team->status == 'closed')) {
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	  } 	
    	  $team->config = JSON_decode($team->config);	
    	  if (!isset($team->config->ranks)) {
    	  		$team->config->ranks = ['admin','manager','president','moderator'];
    	  }
        return view('team.form',
        	["team" => $team,
          "info" => $info
        	]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
    	// jogosultság ellenörzés	
    	$info = Team::getInfo($team->id);
    	if ((!\Auth::user()) | 
    	    (!in_array('admin',$info->userRank)) |
    	    ($info->parentClosed) |
    	    ($request->input('status') == 'closed')) {
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	} 	
    	  
		// tartalmi ellenörzés      
        $request->validate([
            'name' => 'required',
				'ranks' => ['required', new RanksRule()],
            'description' => 'required',
      ]);
      // ranks -ban admin kötelező!
        
  		// rekord array kialakitása
			$teamArr = [];
			$teamArr['parent'] = $request->input('parent');
			$teamArr['name'] = $request->input('name');
			$teamArr['description'] = $request->input('description');
			$teamArr['avatar'] = $request->input('avatar');
			$teamArr['status'] = 'proposal';
			if (\Auth::user()) {
				$teamArr['created_by'] = \Auth::user()->id;
			} else {
				$teamArr['created_by'] = 0;
			}		
			   
			// config kialakitása
			$config = new \stdClass();
			$config->ranks = explode(',',$request->input('ranks'));
			$config->close = $request->input('close');
			$config->memberActivate = $request->input('memberActivate');
			$config->memberExclude = $request->input('memberExclude');
			$config->rankActivate = $request->input('rankActivate');
			$config->rankClose = $request->input('rankClose');
			$config->projectActivate = $request->input('projectActivate');
			$config->productActivate = $request->input('productActivate');
			$config->subTeamActivate = $request->input('subTeamActivate');
			$config->debateActivate = $request->input('debateActivate');
			$teamArr['config'] = JSON_encode($config);
			        

			// teams rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
			 	$model = new Team();
			 	$model->update($teamArr);
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = $exception->errorInfo;
			}			
		
			// result kialakítása		
			if ($errorInfo == '') {
        		$result = redirect()->route('parents.teams.index', ['parent' => $teamArr['parent']])
                        ->with('success',__('team.successSave'));
			} else {
        		$result = redirect()->route('parents.teams.index', ['parent' => $teamArr['parent']])
                        ->with('error',$errorInfo);
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
    	// jogosultság ellenörzés	
    	$info = Team::getInfo($team->id);
    	if ((!\Auth::user()) | 
    	    (in_array('admin',$info->userRank)) |
    	    ($info->parentClosed) |
    	    ($request->input('status') == 'closed')) {
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	} 	
      // $team->delete();
      return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('success','Csoport nem törölhető');
    }
}


