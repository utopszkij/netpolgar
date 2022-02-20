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
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;

class TeamController extends Controller {

	protected $model = false;

	function __construct() {
	    // parent::__construct();
		$this->model = new Team();	
	}	
	
	/**
	 * adatbázis init
	 * @param User record $sysAdmin
	 */
	protected function initDb($sysAdmin) {
	    $teamModel = new Team();
	    $memberModel = new Member();
	    $teamRec = Team::emptyRecord();
	    
	    $team = $teamModel->create([
	        "name" => "Regisztrált felhasználók",
	        "parent" => 0,
	        "description" => "minden regisztrált felhasználó tagja ennek a csoportnak",
	        "avatar" => "/img/team.png",
	        "status" => "active",
	        "config" => JSON_encode($teamRec->config),
	        "activated_at" => date('Y-m-d'),
	        "created_by" => $sysAdmin->id
	    ]);
	    $memberModel->create([
	        "parent_type" => "teams",
	        "parent" => $team->id,
	        "user_id" => $sysAdmin->id,
	        "rank" => "member",
	        "status" => "active",
	        "activated_at" => date('y-m-d'),
	        "created_by" => $sysAdmin->id
	    ]);
	    $memberModel->create([
	        "parent_type" => "teams",
	        "parent" => $team->id,
	        "user_id" => $sysAdmin->id,
	        "rank" => "admin",
	        "status" => "active",
	        "activated_at" => date('y-m-d'),
	        "created_by" => $sysAdmin->id
	    ]);
	    
	    $team = $teamModel->create([
	        "name" => "System admins",
	        "parent" => 0,
	        "description" => "rendszer adminisztrátorok",
	        "avatar" => "/img/team.png",
	        "status" => "active",
	        "config" => JSON_encode($teamRec->config),
	        "activated_at" => date('Y-m-d'),
	        "created_by" =>  $sysAdmin->id0
	    ]);
	    $memberModel->create([
	        "parent_type" => "teams",
	        "parent" => $team->id,
	        "user_id" => $sysAdmin->id,
	        "rank" => "member",
	        "status" => "active",
	        "activated_ar" => date('y-m-d'),
	        "created_by" => $sysAdmin->id
	    ]);
	    $memberModel->create([
	        "parent_type" => "teams",
	        "parent" => $team->id,
	        "user_id" => $sysAdmin->id,
	        "rank" => "admin",
	        "status" => "active",
	        "activated_ar" => date('y-m-d'),
	        "created_by" => $sysAdmin->id
	    ]);
	    
	    
	    
	}
	
    /**
     * lista form megjelenítése
     * @return \Illuminate\Http\Response
     */
    public function index(string $parent = '0') {
    	$data = $this->model->getData((int)$parent, 8);
    	$info = $this->model->getInfo((int)$parent);
    	// ha még nincs egyetlen csoport sem akkor létrehozza őket
    	// creator user és admin az ID szerint első user
    	if ((Team::count() < 1) & (User::count() > 0)) {
    	    $sysAdmin = User::orderBy('id')->first();
    	    $this->initDb($sysAdmin);
    	}
    	
		// jogosultság ellenörzés
	   if (!$this->checkAccess('list', $this->model, $info)) {
	   	return redirect()->to('/')->with('error','team.accessDenied');	    	  
	   }
	   if (!defined('UNITTEST')) {
	       \Request::session()->put('teamIndexUrl',\URL::current());
	   }
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
		if (!defined('UNITTEST')) {
		    \Request::session()->put('teamIndexParentType','users');
	        \Request::session()->put('teamIndexUrl',\URL::current());
		}
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
    	  // ha parent = 0 akkor a sysadmin csoport beli jogokat kell használni
    	  if ($parent > 0) {
    	      $info = $this->model->getInfo((int)$parent);
    	  } else {
    	      $info = $this->model->getInfo(2);
    	  }
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
            $parent = $request->input('parent');
            // ha parent = 0 akkor a sysadmin csoport beli jogokat kell használni
            if ($parent > 0) {
                $info = $this->model->getInfo((int)$parent);
            } else {
                $info = $this->model->getInfo(2);
            }

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


