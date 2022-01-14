<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination;

class ProductController extends Controller {
	
   /**
	* távoli file infok lekérdezése teljes letöltés nélkül
	* csak 'http' -vel kezdödő linkeket ellenöriz
	* @param string $url
	* @return array ['fileExist', 'fileSize' ]
	*/
	protected function getRemoteFileInfo($url) {
		if (substr($url,0,4) == 'http') {
		   $ch = curl_init($url);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		   curl_setopt($ch, CURLOPT_HEADER, TRUE);
		   curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		   $data = curl_exec($ch);
		   $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		   $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		   curl_close($ch);
		   $result = [
	        'fileExists' => (int) $httpResponseCode == 200,
	        'fileSize' => (int) $fileSize
		   ];
		} else {
		   $result = [
	        'fileExists' => 1,
	        'fileSize' => 100
		   ];
		}
		return $result;
	}
		
	/**
	* bejelentkezett user admin a team -ben?
	* @param Project $product
	* @return bool
	*/
	protected function userAdmin($product): bool {
		$result = false;
      $user = \Auth::user();
      if ($user) {
      	if ($product->parent_type == 'teams') {	
				$result = (\DB::table('members')
									->where('parent_type','=','teams')
									->where('parent','=',$product->parent)
									->where('user_id','=',$user->id)
									->where('rank','=','admin')
									->where('status','=','active')
									->count() > 0);		
			} else {
				$result = ($user->id == $product->parent);			
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
      if ($teamId > 0) {  		
         $product = JSON_decode('{ "parent_type": "teams",
         							     "parent": '.$teamId.' }');
         $userAdmin = $this->userAdmin($product);							     		
			$data = Product::getData($teamId, $orderArr, $search,
				$categories, $userAdmin, $page, 8);
		} else {
			$data = Product::getData($teamId, $orderArr, $search,
				$categories, false, $page, 8);
		}	
		foreach ($data->items as $product) {
			$product->userAdmin = $this->userAdmin($product);		
		}	
      return view('product.index',
        	["data" => $data,
        	"teamId" => $teamId,
        	"team" => $team,
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

    		// csak bejelentkezett user vihet fel
    		if (!\Auth::user()) {
				return redirect()->to('/teams/'.$team->id)
			                 ->with('error',__('product.accessDenied'));
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
	 * product rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 protected Function saveOrStore(int &$id, Request $request): string {	
			// rekord array kialakitása
			$parent = $request->input('parent');
			$parent_type = $request->input('parent_type');
			$user = \Auth::user();
			$productArr = [];
			$productArr['parent_type'] = $parent_type;
			$productArr['parent'] = $parent;
			$productArr['name'] = strip_tags($request->input('name'));
			$productArr['description'] = strip_tags($request->input('description',['br']));
			$productArr['avatar'] = strip_tags($request->input('avatar'));
			$fileInfo = $this->getRemoteFileInfo($productArr['avatar']);
			if ($fileInfo['fileSize'] > 1000000) {
				$productArr['avatar'] = '/img/noimage.png';			
			}			
			
			$productArr['unit'] = strip_tags($request->input('unit'));
			$productArr['price'] = $request->input('price');
			$productArr['currency'] = strip_tags($request->input('currency'));
			$productArr['status'] = strip_tags($request->input('status'));

			// product rekord tárolás az adatbázisba
			$errorInfo = '';
			try {
				$model = new Product();
				if ($id == 0) {
					$productArr['stock'] = 0;
			 		$productRec = $model->create($productArr);
			 		$id = $productRec->id;
			 	} else {
					$model->where('id','=',$id)->update($productArr);			 	
			 	}	
			} catch (\Illuminate\Database\QueryException $exception) {
			    $errorInfo = $exception->errorInfo;
			}
			if ($user) {	
				if ($errorInfo == '') {
					$addToStock = $request->input('quantity','');
					if ($addToStock != 0) {
						\DB::table('productadds')
						->insert(['product_id' => $id,
						'quantity' => $addToStock,
						'user_id' => $user->id 
						]);
						$product = $model->where('id','=',$id)->first();
						$product->stock = $product->stock + $addToStock;
						$model->where('id','=',$id)
						->update([
							'stock' => $product->stock						
						]);
					}			
				}
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
    public function store(Request $request) {
    		if (!\Auth::user()) { 
				return redirect()->to('/products/creaate/'.$request->input('team_id'))
			    ->with('error',__('product.accessDenied'));
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'parent' => 'required',
				'description' => 'required',
				'unit' => 'required',
				'price' => ['required','numeric','min:0'],
				'currency' => 'required'
			]);
			if ($request->input('parent_type') == 'teams') {
				$teamId = $request->input('parent');
			} else {
				$teamId = 0;			
			}	 
			// Product rekord kiirása
			$id = 0;
			$errorInfo = $this->saveOrStore($id,$request);
			
			 // categories tárolása
			 $oldCategories = Product::getCategories($id);
			 $categories = $request->input('categories');
			 if ($categories != $oldCategories) {
				$errorIndo = Product::saveCategories($id, $categories);			 
			 }

		   // result kialakitása			
			if ($errorInfo == '') { 
				$result = redirect()->to('/products/list/'.$teamId)
			                 ->with('success',__('product.successSave') );
			} else {
				$result = redirect()->to('/products/list/'.$teamId)
			                 ->with('error',$errorInfo);
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
        return view('product.show',
        	["product" => $product,
        	 "parentUser" => $parentUser,
        	 "team" => $team,
        	 "categories" => $categories,
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
		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      (!$this->userAdmin($product)) |
    	      ($product->status == 'closed')) {
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

        return view('product.form',
        	["product" => $product,
        	 "parentUser" => $parentUser,
        	 "team" => $team,
          "info" => $info,
          "categories" => $categories,
          "userAdmin" => $this->userAdmin($product),
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
		  // Jogosultság ellenörzés
    	  if ((!\Auth::user()) |
    	      (!$this->userAdmin($product)) |
    	      ($product->status == 'closed')) {
	        return redirect()->to('/products/'.$product->id)
                        ->with('error',__('product.accessDenied'));
    	  } 	
    	  
			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'parent' => 'required',
				'description' => 'required',
				'unit' => 'required',
				'price' => ['required','numeric','min:0'],
				'currency' => 'required'
			]);
			if ($request->input('parent_type') == 'teams') {
				$teamId = $request->input('parent');
			} else {
				$teamId = 0;			
			}	 

			// Product rekord kiirása
			$id = $product->id;
			$errorInfo = $this->saveOrStore($id,$request);
			
			 // categories tárolása
			 $oldCategories = Product::getCategories($id);
			 $categories = $request->input('categories');
			 if ($categories != $oldCategories) {
				$errorIndo = Product::saveCategories($id, $categories);			 
			 }

		   // result kialakitása			
			if ($errorInfo == '') { 
				$result = redirect()->to('/products/list/'.$teamId)
			                 ->with('success',__('product.successSave') );
			} else {
				$result = redirect()->to('/products/list/'.$teamId)
			                 ->with('error',$errorInfo);
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
		// EZ MÉG NINCS KÉSZ!
		
    	// jogosultság ellenörzés	
    	$teamId = 0;
    	if ($product->parent_type == 'teams') {
			$teamId = $product->parent;   	
    	}
    	$info = Product::getInfo($product);
    	if ((!\Auth::user()) | 
    	    (!$this->userAdmin($product)) |
    	    ($team->status == 'closed')) {
	        return redirect()->to('/products/list/'.$teamId)
                        ->with('error',__('product.accessDenied'));
    	} 	
      // product és hozzá tartozó alrekordok törlése
      // .......
      return redirect()->to('/products/list/'.$teamId)
                        ->with('success',__('product.deleted'));
    }
}


