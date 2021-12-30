<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination;

class ProductController extends Controller {
	
	/**
	* bejelentkezett user admin a team -ben?
	* @param TeamRec|false $team
	* @return bool
	*/
	protected function userAdmin($team): bool {
		$result = false;
      $user = \Auth::user();
      if ($user) {
			if ($team) {
				$result = (\DB::table('members')
									->where('parent_type','=','teams')
									->where('parent','=',$team->id)
									->where('user_id','=',$user->id)
									->where('rank','=','admin')
									->where('status','=','active')
									->count() > 0);			
			}
      } 
      return $result;
	}
	
    /**
     * Display a listing of the resource.
     * @param Request (page,order, categories , teamId)
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $teamId)  {
    	
    	if (\URL::to('/') != 'http://localhost:8000') {
	    	return redirect()->to('/construction');
	   } 	
    	
		$order = $request->input('order', 
										 $request->session()->get('productsOrder','name,asc'));
		$orderArr = explode(',',$order);										     	
		$search = $request->input('search', 
										 $request->session()->get('productsSearch',''));    	
		$categories = $request->input('categories', 
										 $request->session()->get('productsCategories',''));
		$request->session()->put('productsOrder',$order); 
		$request->session()->put('productsSearch',$search); 
		$request->session()->put('productsCategories',$categories); 
		$request->session()->put('productsTeamId',$teamId); 
		$page = $request->input('page',1);    	

      if ($teamId == '') {
			$team = false;
      } else {
      	$team = \DB::table('teams')->where('id','=',$teamId)->first();
      }  			
      $userAdmin = $this->userAdmin($team);

		//TEST
		$orderArr = ['value','desc'];

		$sql = '
		select products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status,
		       avg(evaluations.value) value
		from products       
		left outer join evaluations on evaluations.product_id = products.id       
		left outer join productcats on productcats.product_id = products.id
		where 1 ';
		if (!$userAdmin) {
			$sql .= ' and products.status = "active"';		
		}
		if ($search != '') {
			$sql .= ' and products.name like "%'.$search.'%" ';
		}
		if ($categories != '') {	
			$sql .= ' and productcats.category in ('.$categories.') ';
		}	  
		if ($teamId != '') {
		 	$sql .= ' and products.team_id = '.$teamId.' ';
		}      
		$sql .= '
		group by products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status 
		order by '.$orderArr[0].' '.$orderArr[1];
		
		// paginate from raw sql
		$array_of_objects = \DB::select($sql);
		if (!$array_of_objects) {
			$array_of_objects = [];
		}
		
		// test
		$array_of_objects[] = JSON_decode('
			{"id":1,
			 "name":"testTermék",
			 "status":"active",
			 "avatar":"/img/noavatar.png",
			 "price":34,
			 "stock":2,
			 "unit":"db",
			 "value":4.3
			}		
		');
		
		
		$collection = collect($array_of_objects);
//    $sorted = $collection;
	   $num_per_page = 8;
//    $offset = ( $page - 1) * $num_per_page;
//    $sorted = $sorted->splice($offset, $num_per_page);
      $data = new \Illuminate\Pagination\Paginator(
      	$collection,
      	$num_per_page, 
      	$page);

     
      return view('product.index',
        	["data" => $data,
        	"teamId" => $teamId,
        	"team" => $team,
        	"order" => $order,
        	"search" => $search,
        	"categories" => $categories,
        	"userAdmin" => $userAdmin])
         ->with('i', (request()->input('page', 1) - 1) * 8);
    }

    /**
     * Show the form for creating a new resource.
     * @param TeamRecord
     * @return \Illuminate\Http\Response
     */
    public function create($team) {
    	  $product = Product::emptyRecord();
    	  $product->team_id = $team->id;	
    	  $info = Product::getInfo($product);
    		// csak parent csoport tag vihet fel
    		if ((!\Auth::user()) | 
    		    (!$this->userAdmin($team))) {
				return redirect()->to('/teams/'.$team->id)
			                 ->with('error',__('product.accessDenied'));
    		}

        return view('product.form',
        ["product" => $product,
         "team" => $team,
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
          // id=1 "regisztráltak" csoport speciális kezelése:
          // minden regisztrált user automatikusan tag
          if ($team->id == 1) {
            \DB::statement('insert into members (parent_type, parent, user_id, `status`, `rank`, created_by) 
              select "teams", 1, users.id, "active", "member", users.id
              from users
              left outer join members on members.parent_type = "teams" and
                                        members.parent = 1 and members.user_id = users.id
              where members.id is null
            ');  
          }
          
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


