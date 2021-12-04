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
    public function create(string $parent = '0')
    {
    	
    	  $team = Team::emptyRecord();
    	  $team->parent = $parent;	
    	  $info = Team::getInfo($parent);
    		// csak parent csoport tag vihet fel
    		if ((!\Auth::user()) | 
    		    (!$this->userMember($info->userRank)) | 
    		    ($info->parentClosed)) {
				return redirect()->route('parents.teams.index', ['parent' => $parent])
			                 ->with('error',__('team.accessDenied'));
    		}

        return view('team.form',
        ["team" => $team,
         "info" => $info]);
    }

	 /**
	 * team rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 protected Function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			$teamArr = [];
			$teamArr['parent'] = $request->input('parent');
			$teamArr['name'] = $request->input('name');
			$teamArr['description'] = $request->input('description');
			$teamArr['avatar'] = $request->input('avatar');
			if ($id == 0) {
				$teamArr['status'] = 'proposal';
				if (\Auth::user()) {
					$teamArr['created_by'] = \Auth::user()->id;
				} else {
					$teamArr['created_by'] = 0;
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
			$config->projectActivate = $request->input('projectActivate');
			$config->productActivate = $request->input('productActivate');
			$config->subTeamActivate = $request->input('subTeamActivate');
			$config->debateActivate = $request->input('debateActivate');
			$teamArr['config'] = JSON_encode($config);

			// teams rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
				$model = new Team();
				if ($id == 0) {
			 		$teamRec = $model->create($teamArr);
			 		$id = $teamRec->id;
			 	} else {
					$model->where('id','=',$id)->update($teamArr);			 	
			 	}	
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = $exception->errorInfo;
			}	
			return $errorInfo;		
	 }	

	 /**
	 * bejelentkezett user legyen admin -ja az $id team -nek
	 * @param int $id
	 * @return string
	 */
    protected function addAdmin(int $id): string {		
				$memberArr = [];
				$memberArr['parent_type'] = 'teams';
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
     *         ['id','parent', 'name','description','avatar','config']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    		// jogosultság ellenörzés
    		$info = Team::getInfo($request->input('parent'));
    		if ((!\Auth::user()) | 
    		    (!$this->userMember($info->userRank)) |
    		    ($info->parentClosed)) {
				return redirect()->route('parents.teams.index', ['parent' => $request->input('parent')])
			    ->with('error',__('team.accessDenied'));
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'ranks' => ['required', new RanksRule()],
				'description' => 'required'
			]);

			// team rekord kiirása
			$id = 0;
			$errorInfo = $this->saveOrStore($id,$request);
			
			// a létrehozó (bejelentkezett) user "admin" tagja a csoportnak
			// members rekord tárolás az adatbázisba
			if ($errorInfo == '') {
				$errorInfo = $this->addAdmin($id);
			}    

		   // result kialakitása			
			if ($errorInfo == '') { 
				$result = redirect()->route('parents.teams.index', ['parent' => $request->input('parent')])
			                 ->with('success',__('team.successSave') );
			} else {
				$result = redirect()->route('parents.teams.index', ['parent' => $request->input('parent')])
			                 ->with('error',$errorInfo);
			}
			return $result;                 
    }
    
    /**
    * team->config json string dekodolása
    * @param Team $team
    * @return void
    */      
    
    protected function decodeConfig(Team &$team) {
    	  $team->config = JSON_decode($team->config);
    	  if (!isset($team->config->ranks)) {
    	  		$team->config->ranks = ['admin','manager','president','moderator'];
    	  } else if (is_string($team->config->ranks)) {
				$team->config->ranks = explode(',',$team->config->ranks);
    	  }
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
		  $this->decodeConfig($team, $info);
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
		  $this->decodeConfig($team, $info);
    	  if ($info->parentClosed) {
				$team->status = 'closed';    	  
    	  }	

		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      (!$this->userAdmin($info->userRank)) |
    	      ($info->parentClosed) |
    	      ($team->status == 'closed')) {
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
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
    	    (!$this->userAdmin($info->userRank)) |
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

		// team rekord kiirása
		$id = $team->id;
		$errorInfo = $this->saveOrStore($id, $request);
		
		// result kialakítása		
		if ($errorInfo == '') {
      	$result = redirect()->route('parents.teams.index', ['parent' => $team->parent])
                      ->with('success',__('team.successSave'));
		} else {
      	$result = redirect()->route('parents.teams.index', ['parent' => $team->parent])
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
    	    (!$this->userAdmin($info->userRank)) |
    	    ($info->parentClosed) |
    	    ($team->status == 'closed')) {
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	} 	
      // $team->delete();
      return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('success','Csoport nem törölhető');
    }
}


