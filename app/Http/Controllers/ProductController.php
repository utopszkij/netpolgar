<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Pagination;
use App\Models\Minimarkdown;

class ProductController extends Controller {
	
	/**
	 * bejelentkezett user jogosult erre?
	 * @param string $action 'cretae'|'edit'|'delete'
	 * @param $team
	 * @param $product
	 * @return $bool
	 */ 
	protected function accessCheck(string $action, $team, $product): bool {
		$result = false;
		if ($action == 'create') {
			if (\Auth::check()) {
				if ($team) {
					$result = Team::userAdmin($team->id, \Auth::user()->id);
				} else {
					$result = true;
				}
			}
		}
		if ($action == 'edit') {
			if (\Auth::check()) {
				$result = Product::userAdmin($product, \Auth::user()->id);
			}	
		}
		if ($action == 'delete') {
		}
		return $result;
	}
	
    /**
     * Display a listing of the resource.
     * @param Request (page,order, categories , teamId)
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $teamId)  {
	   
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
		$request->session()->put('productsUserId',0); 
		$page = $request->input('page',1);    	
        if ($teamId == '') {
			$team = false;
        } else {
      	   $team = \DB::table('teams')->where('id','=',$teamId)->first();
        }
        if ($teamId > 0) {  		
           $product = JSON_decode('{ "parent_type": "teams",
         							     "parent": '.$teamId.' }');
           $userAdmin = Product::userAdmin($product);							     		
		   $data = Product::getData($teamId, $orderArr, $search,
		   $categories, $userAdmin, $page, 8);
		} else {
			$data = Product::getData($teamId, $orderArr, $search,
			$categories, false, $page, 8);
		}	
		foreach ($data->items as $product) {
			$product->userAdmin = Product::userAdmin($product);
			// product készlet meghatározás
			$product->stock = Product::getStock($product);
		}	
	  $request->session()->put('productsListUrl',\URL::current());	
      return view('product.index',
        	["data" => $data,
        	"teamId" => $teamId,
        	"team" => $team,
        	"user" => false,
        	"order" => $order,
        	"search" => $search,
        	"categories" => $categories]);
    }
    
    /**
     * Display a listing of the resource userId szerint.
     * @param Request 
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function listByUser(Request $request, $userId)  {
	   
		$order = $request->input('order', $request->session()->get('productsOrder','name,asc'));
		$orderArr = explode(',',$order);										     	
		$search = $request->input('search', $request->session()->get('productsSearch',''));    	
		$categories = $request->input('categories', $request->session()->get('productsCategories',''));
		$request->session()->put('productsOrder',$order); 
		$request->session()->put('productsSearch',$search); 
		$request->session()->put('productsCategories',$categories); 
		$request->session()->put('productsTeamId',0); 
		$request->session()->put('productsUserId',$userId); 
		$page = $request->input('page',1);    	
		$team = false;
	    $data = Product::getDataByUser($userId, $orderArr, $search,
				$categories, false, $page, 8);
	    foreach ($data->items as $product) {
			$product->userAdmin = Product::userAdmin($product);
			// product készlet meghatározás
			$product->stock = Product::getStock($product);
	    }	
	    $request->session()->put('productsListUrl',\URL::current());	
	    $user = \DB::table('users')->where('id','=',$userId)->first();
        return view('product.index',
        	["data" => $data,
        	"teamId" => 0,
        	"team" => false,
        	"user" => $user,
        	"order" => $order,
        	"search" => $search,
        	"categories" => $categories]);
    }
    

    /**
     * Show the form for creating a new resource.
     * @param team Record
     * @return \Illuminate\Http\Response
     */
    public function create($team) {

		 if (is_string($team)) {
			$team = Product::where('id','=',$team)->first();
		 }
		 if (!$this->accessCheck('create', $team, false)) {
				return redirect()->to('/teams/'.$team->id);
		 }
    	  
    	 $product = Product::emptyRecord();
    	 if (is_string($team)) {
				$team = \DB::table('teams')->where('id','=',$team)->first();    	  
    	 } else {
				$team = false;    	  
    	 } 
    	 if ($team) {
    	  		$product->parent_type = 'teams';
    	  		$product->parent = $team->id;
    	  		$parentUser = false;
    	 } else {
    	  		$product->parent_type = 'users';
				$product->parent = \Auth::user()->id;
				$parentUser = \Auth::user();    	  
    	 }			
    	 $info = Product::getInfo($product);
    	 $categories = Product::getCategories($product->id);

        return view('product.form',
        ["product" => $product,
         "team" => $team,
         "parentUser" => $parentUser,
         "categories" => $categories,
         "info" => $info]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     *         ['id','parent', 'name','description','avatar','config']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
		
		 if ($request->input('parent_type') == 'teams') {
			$teamId = $request->input('parent');
			$team = \DB::table('teams')->where('id','=',$teamId)->first();
			if (!$this->accessCheck('create', $team, false)) {
				return redirect()->to('/products/creaate/'.$request->input('team_id'))
			    ->with('error',__('product.accessDenied'));
			}
    	 } else {
			$teamId = 0;			
			$user = \Auth::user();
			if (!$this->accessCheck('create', false, $user)) {
				return redirect()->to('/products/creaate/'.$request->input('team_id'))
			    ->with('error',__('product.accessDenied'));
			}
		 }	
			$model = new Product();
			$model->valid($request);
			
				 
			// Product rekord kiirása
			$id = 0;
			$errorInfo = Product::saveOrStore($id,$request);
			
			 // categories tárolása
			 $oldCategories = Product::getCategories($id);
			 $categories = $request->input('categories');
			 if ($categories != $oldCategories) {
				$errorIndo = Product::saveCategories($id, $categories);			 
			 }

		     // result kialakitása			
			 if ($request->input('parent_type') == 'teams') {
				 if ($errorInfo == '') { 
					$result = redirect()->to('/products/list/'.$teamId)
								 ->with('success',__('product.successSave') );
				 } else {
					$result = redirect()->to('/products/list/'.$teamId)
								 ->with('error',$errorInfo);
				 }
			 } else {
				 if ($errorInfo == '') { 
					$result = redirect()->to('/products/listbyuser/'.$user->id)
								 ->with('success',__('product.successSave') );
				 } else {
					$result = redirect()->to('/products/listbyuser/'.$user->id)
								 ->with('error',$errorInfo);
				 }
			 }	 
			 return $result;                 
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)   {
    	  if ($product->parent_type == 'teams') {	
        		$team = \DB::table('teams')->where('id','=',$product->parent)
		        ->first();
		      $parentUser = false;  
        } else {
				$team = false;
				$parentUser = \DB::table('users')
					->where('id','=',$product->parent)
					->first();        
        }		
        $categories = Product::getCategories($product->id);									    
    	  $info = Product::getInfo($product); 
    	$product->stock = Product::getStock($product);  
        return view('product.show',
        	["product" => $product,
        	 "parentUser" => $parentUser,
        	 "team" => $team,
        	 "categories" => $categories,
        	 "userUsed" => true,
        	 "info" => $info
        	]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
		 if (!$this->accessCheck('edit', false, $product)) {
	        return redirect()->to('/products/'.$product->id)
                        ->with('error',__('product.accessDenied'));
    	 }
    	  	
    	 $info = Product::getInfo($product);
    	 if ($product->parent_type == 'teams') {	
        		$team = \DB::table('teams')->where('id','=',$product->parent)
		        ->first();
		      $parentUser = false;  
         } else {
				$team = false;
				$parentUser = \DB::table('users')
					->where('id','=',$product->parent)
					->first();        
         }		
    	 $categories = Product::getCategories($product->id);
		 $product->stock = Product::getStock($product);
         return view('product.form',
        	["product" => $product,
        	 "parentUser" => $parentUser,
        	 "team" => $team,
			 "info" => $info,
			 "categories" => $categories,
			 "userAdmin" => Product::userAdmin($product),
        	]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TProduct  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
		if (!$this->accessCheck('edit', false, $product)) {
	        return redirect()->to('/products/'.$product->id)
                        ->with('error',__('product.accessDenied'));
    	}
    	  
		$model = new Product();
		$model->valid($request);

		if ($request->input('parent_type') == 'teams') {
				$teamId = $request->input('parent');
		} else {
				$teamId = 0;			
		}	 

		// Product rekord kiirása
		$id = $product->id;
		$errorInfo = Product::saveOrStore($id,$request);
			
		// categories tárolása
		$oldCategories = Product::getCategories($id);
		$categories = $request->input('categories');
		if ($categories != $oldCategories) {
				$errorIndo = Product::saveCategories($id, $categories);			 
		}

		// result kialakitása		
		if ($product->parent_type == 'teams') {	
			if ($errorInfo == '') { 
					$result = redirect()->to('/products/list/'.$teamId)
								 ->with('success',__('product.successSave') );
			} else {
					$result = redirect()->to('/products/list/'.$teamId)
								 ->with('error',$errorInfo);
			}
		} else {
			$user = \Auth::user();
			if ($errorInfo == '') { 
					$result = redirect()->to('/products/listbyuser/'.$user->id)
								 ->with('success',__('product.successSave') );
			} else {
					$result = redirect()->to('/products/listbyuser/'.$user->id)
								 ->with('error',$errorInfo);
			}
		}
		return $result;                 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {

        return redirect()->to('/construction');

		// EZ MÉG NINCS KÉSZ!
		
    	// jogosultság ellenörzés	
    	$teamId = 0;
    	if ($product->parent_type == 'teams') {
			$teamId = $product->parent;   	
    	}
    	$info = Product::getInfo($product);
    	if ((!\Auth::user()) | 
    	    (!Product::userAdmin($product)) |
    	    ($team->status == 'closed')) {
	        return redirect()->to('/products/list/'.$teamId)
                        ->with('error',__('product.accessDenied'));
    	} 	
      // product és hozzá tartozó alrekordok törlése
      // .......
      return redirect()->to('/products/list/'.$teamId)
                        ->with('success',__('product.deleted'));
    }
    
    /**
     * értékelő form (csak olyannak megengedett aki "vásárolt" az adott termékből)
     * @param int $productId
     * @return laravel view|redirect
     */ 
    public function evaluation(int $productId) {
        $product = Product::where('id','=',$productId)->first();
        $info = Product::getInfo($product);
        if ($info->userUsed) {
			$userEvaluated = (\DB::table('evaluations')
				->where('parent','=',$productId)
			    ->where('parent_type','=','products')
				->where('user_id','=',\Auth::user()->id)
				->count() > 0);
			if (!$userEvaluated) {
				$result = view('product.evaluation',[
					"product" => $product,
					"backUrl" => \URL::previous()
				]);
			} else {
				$result = redirect()->to(\URL::previous())
				->with('error',__('product.evaluationExists'));
			}	
		} else {
			$result = redirect()->to(\URL::previous())
				->with('error',__('product.evaluationDisabled'));
		}
		return $result;
	}	
	
	/**
	 * Értékelés tárolása
	 * @param Request projectId, evaluation, backUrl
	 * @return larevel redirect
	 */ 
	public function saveevaluation(Request $request) {
		$productId = $request->input('productId',0);
		$backUrl = $request->input('backUrl','');
        $product = Product::where('id','=',$productId)->first();
        $info = Product::getInfo($product);
        if ($info->userUsed) {
			$userEvaluated = (\DB::table('evaluations')
				->where('parent','=',$productId)
			    ->where('parent_type','=','products')
				->where('user_id','=',\Auth::user()->id)
				->count() > 0);
			if (!$userEvaluated) {
				$t = \DB::table('evaluations');
				$t->insert([
				"parent" => $product->id,
				"parent_type" => "products",    
				"user_id" => \Auth::user()->id,
				"value" => $request->input('evaluation',1)
				]);
				$result = redirect()->to($backUrl)
				->with('success',__('product.evaluationSaved'));
			} else {
				$result = redirect()->to($backUrl)
				->with('error',__('product.evaluationExists'));
			}	
		} else {
			$result = redirect()->to($backUrl)
				->with('error',__('product.evaluationDisabled'));
		}
		return $result;
	}

}


