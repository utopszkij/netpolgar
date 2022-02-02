<?php
/**
* Csoportok kontroller
* Public functions:
*    index($parent)
*    create($parent)
*    store($request)
*    show($team)
*    edit($team)
*    update($request, $team)
*    destroy($team)
*/
namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller {

	protected $model = false;

	function __construct() {
		$this->model = new Team();	
	}	
	
    /**
     * lista form megjelenítése
     * @return \Illuminate\Http\Response
     */
    public function index(string $parent = '0') {
    	$data = $this->model->getData((int)$parent, 8);
    	$info = $this->model->getInfo((int)$parent);

		// jogosultság ellenörzés
	   if (!$this->checkAccess('list', $this->model, $info)) {
	   	return redirect()->to('/')->with('error','team.accessDenied');	    	  
		}

	 \Request::session()->put('teamIndexUrl',\URL::current());
      return view('team.index',
        	["data" => $data,
        	"parentType" => 'teams',
        	"parent" => $parent,
        	"info" => $info])
         ->with('i', (request()->input('page', 1) - 1) * 8);
    }
    
    /**
     * lista azon csoportokról amiknek userId tagja
     * @param int $userId 
     * @return laravel view
     */ 
    public function listByUser(int $userId) {
    	$data = $this->model->getDataByUser((int)$userId, 8);
    	$info = $this->model->getInfoByUser((int)$userId);
		$user = \DB::table('users')->where('id','=',$userId)->first();
		\Request::session()->put('teamIndexParentType','users');
	  \Request::session()->put('teamIndexUrl',\URL::current());
      return view('team.index',
        	["data" => $data,
        	"parentType" => 'users',
        	"parent" => $user,
        	"info" => $info])
         ->with('i', (request()->input('page', 1) - 1) * 8);
	}
    
    /** 
     * fa struktúra megjelenítő
     * @return laravel view
     */ 
    public function tree() {
		$data = $this->model->getTree();
		return view('team.tree',
        	["data" => $data]);
	}

    /**
     * Új felvitel form megjelenítése
     * @return \Illuminate\Http\Response
     */
    public function create(string $parent = '0') {
    	  $team = $this->model->emptyRecord();
    	  $team->parent = $parent;	
    	  $info = $this->model->getInfo((int)$parent);

		  // jogosultság ellenörzés	
		  if (!$this->checkAccess('add', $this->model, $info)) {	 
				return redirect()->route('parents.teams.index', 
					['parent' => $parent])
			   ->with('error',__('team.accessDenied'));
    	  }

        return view('team.form',
        ["team" => $team,
         "info" => $info]);
    }

    /**
     * Újonnan felviendő adat tárolása az adatbázisba
     * @param  \Illuminate\Http\Request  $request 
     *         ['id','parent', 'name','description','avatar','config']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)  {
    		
    		// jogosultság ellenörzés
    		$info = $this->model->getInfo($request->input('parent'));
		   if (!$this->checkAccess('add', $this->model, $info)) {	    	  
				return redirect()->route('parents.teams.index', 
				  ['parent' => $request->input('parent')])
			   ->with('error',__('team.accessDenied'));
    		}
    		
			if ($this->model->valid($request)) {
				$errorInfo = $this->model->updateOrCreate($request);
	
			   // result kialakitása			
				if ($errorInfo == '') { 
					$result = redirect()->route('parents.teams.index',
						 ['parent' => $request->input('parent')])
				   ->with('success',__('team.successSave') );
				} else {
					$result = redirect()->route('parents.teams.index', 
						 ['parent' => $request->input('parent')])
				   ->with('error',$errorInfo);
				}
			}
			return $result;                 
    }

    /**
     * Adat megjelenitő form 
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team) {
		// id=1 "regisztráltak" csoport speciális kezelése:
     	// minden regisztrált user automatikusan tag
     	if ($team->id == 1) {
       	$this->model->adjustRegisteredTeamMembers();
      }
        
      // jogosultság ellenörzés    
    	$info = $this->model->getInfo($team->id); 
	   if (!$this->checkAccess('show', $team, $info)) {
	   	return redirect()->to('/')->with('error','team.accessDenied');	    	  
		}
		    	
		$this->model->decodeConfig($team, $info);
     	if ($info->parentClosed) {
				$team->status = 'closed';    	  
    	}	
      return view('team.show',
        	["team" => $team,
        	 "info" => $info
        	]);
    }

    /**
     * Adat módosító form megjelenítése.
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)  {
    	$info = $this->model->getInfo($team->id);
		$this->model->decodeConfig($team, $info);
    	if ($info->parentClosed) {
				$team->status = 'closed';    	  
    	}	

		// jogosultság ellenörzés
	   if (!$this->checkAccess('edit', $team, $info)) {	
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	} 	

      return view('team.form',
        	["team" => $team,
          "info" => $info
        	]);
    }

    /**
     * Rekord modosítás az adatbázisban.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team) {

    	// jogosultság ellenörzés	
    	$info = $this->model->getInfo($team->id);
	   if (!$this->checkAccess('edit', $team, $info)) {	    	  
	        return redirect()->route('parents.teams.index', ['parent' => $team->parent])
                        ->with('error',__('team.accessDenied'));
    	} 	
    	  
		if ($this->model->valid($request)) {
			// team rekord kiirása
			$errorInfo = $this->model->updateOrCreate($request);
			
			// result kialakítása
			$parent = $request->input('parent',0);
			if ($errorInfo == '') {
	      	$result = redirect()->route('parents.teams.index', 
	      		['parent' => $parent])
	         ->with('success',__('team.successSave'));
			} else {
	      	$result = redirect()->route('parents.teams.index',
	      		['parent' => $parent])
	         ->with('error',$errorInfo);
			}
		}
		return $result;                        
    }

    /**
     * Rekord törlés
     * ennél a rekord tipusnál nem megengedett
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team) {
      return redirect()->route('parents.teams.index',
      	 ['parent' => $team->parent])
      ->with('success','Csoport nem törölhető');
    }
    
    /**
    * bejelentkezett user jogosult erre a müveletre?
    * @param string $action 'add'|'edit'|'show'|'delete'|'list'
    * @param Team $team
    * @param infoObject $info
    * @return bool
    */
    protected function checkAccess(string $action, Team $team, $info ): bool {
    	$result = false;
    	if ($action == 'add') {
    		// csak parent csoport tag vihet fel
    		$result = (\Auth::check() & 
    		    ($info->userMember) & 
    		    (!$info->parentClosed));
    	}
		if ($action == 'edit') {
    		$result = (\Auth::check() &
    	      ($info->userAdmin) &
    	      (!$info->parentClosed) &
    	      ($team->status != 'closed'));
    	}
		if ($action == 'show') {
    		$result = true;
    	}	
		if ($action == 'list') {
    		$result = true;
    	}	
		if ($action == 'delete') {
    		$result = false;
    	}	
    	return $result;      
    }
}


