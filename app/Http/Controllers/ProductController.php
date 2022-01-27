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
	* @param TeamRec|false $team
	* @return bool
	*/
	protected function userAdmin($teamId): bool {
		$result = false;
      $user = \Auth::user();
      if ($user) {
				$result = (\DB::table('members')
									->where('parent_type','=','teams')
									->where('parent','=',$teamId)
									->where('user_id','=',$user->id)
									->where('rank','=','admin')
									->where('status','=','active')
									->count() > 0);			
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
      $userAdmin = $this->userAdmin($teamId);
		$data = Product::getData($teamId, $orderArr, $search,
			$categories, $userAdmin, $page, 8);
      return view('product.index',
        	["data" => $data,
        	"teamId" => $teamId,
        	"team" => $team,
        	"order" => $order,
        	"search" => $search,
        	"categories" => $categories,
        	"userAdmin" => $userAdmin]);
    }

    /**
     * Show the form for creating a new resource.
     * @param team Record
     * @return \Illuminate\Http\Response
     */
    public function create($team) {
    	  $product = Product::emptyRecord();
    	  if (is_string($team)) {
				$team = \DB::table('teams')->where('id','=',$team)->first();    	  
    	  } 
    	  
    	  $product->team_id = $team->id;	
    	  $info = Product::getInfo($product);
    	  $categories = Product::getCategories($product->id);
    		// csak parent csoport tag vihet fel
    		if ((!\Auth::user()) | 
    		    (!$this->userAdmin($team->id))) {
				return redirect()->to('/teams/'.$team->id)
			                 ->with('error',__('product.accessDenied'));
    		}

        return view('product.form',
        ["product" => $product,
         "team" => $team,
         "categories" => $categories,
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
			$user = \Auth::user();
			$productArr = [];
			$productArr['team_id'] = $request->input('team_id');
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

			// teams rekord tárolás az adatbázisba
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
    		$userAdmin = $this->userAdmin($request->input('team_id',''));
    		if ((!\Auth::user()) | 
    		    (!$userAdmin)) {
				return redirect()->to('/products/creaate/'.$request->input('team_id'))
			    ->with('error',__('product.accessDenied'));
    		}

			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'team_id' => 'required',
				'description' => 'required',
				'unit' => 'required',
				'price' => ['required','numeric','min:0'],
				'currency' => 'required'
			]);

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
				$result = redirect()->to('/products/list/'.$request->input('team_id'))
			                 ->with('success',__('product.successSave') );
			} else {
				$result = redirect()->to('/products/list/'.$request->input('team_id'))
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
        $team = \DB::table('teams')->where('id','=',$product->team_id)
        ->first();
        $categories = Product::getCategories($product->id);									    
    	  $info = Product::getInfo($product); 
        return view('product.show',
        	["product" => $product,
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
    	      (!$this->userAdmin($product->team_id)) |
    	      ($product->status == 'closed')) {
	        return redirect()->to('/products/'.$product->id)
                        ->with('error',__('product.accessDenied'));
    	  } 	
    	  $info = Product::getInfo($product);
		  $team = \DB::table('teams')->where('id','=',$product->team_id)
		  									  ->first();    	  
    	  $categories = Product::getCategories($product->id);

        return view('product.form',
        	["product" => $product,
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
    	      (!$this->userAdmin($product->team_id)) |
    	      ($product->status == 'closed')) {
	        return redirect()->to('/products/'.$product->id)
                        ->with('error',__('product.accessDenied'));
    	  } 	
    	  
			// tartalmi ellenörzések 
			$request->validate([
				'name' => 'required',
				'team_id' => 'required',
				'description' => 'required',
				'unit' => 'required',
				'price' => ['required','numeric','min:0'],
				'currency' => 'required'
			]);

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
				$result = redirect()->to('/products/list/'.$request->input('team_id'))
			                 ->with('success',__('product.successSave') );
			} else {
				$result = redirect()->to('/products/list/'.$request->input('team_id'))
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
    	// jogosultság ellenörzés	
    	$info = Product::getInfo($product);
    	if ((!\Auth::user()) | 
    	    (!$this->userAdmin($product->team_id)) |
    	    ($team->status == 'closed')) {
	        return redirect()->to('/products/list/'.$product->team_id)
                        ->with('error',__('product.accessDenied'));
    	} 	
      // product és hozzá tartozó alrekordok törlése
      return redirect()->to('/products/list/'.$product->team_id)
                        ->with('success',__('product.deleted'));
    }
}


