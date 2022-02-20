<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Upload;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'parent_type', 'parent', 
        'avatar', 'price', 'currency', 'vat', 'type', 'stock', 'unit', 'status'
    ];
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"parent_type": "teams",
			"parent":0,
			"description":"",
			"avatar":"/img/noimage.png",
			"status":"active",
			"price":0,
			"currency":"HUF",
			"vat":0,
			"type":"",
			"stock":0,
			"unit":"db",
			"created_at":"1900-01-01",
			"updated_at":"1900-01-01"
    	}');
    	return $result;
    }
    
    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Team $product
     * @return void  $result -ot modosítja
     */
    public static function getLikeInfo(&$result, $product): void {
        // like, disLike, memberCount infok
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','products')
        ->where('parent','=',$product->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','product')
        ->where('parent','=',$product->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','product')
                ->where('parent','=',$product->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','product')
                ->where('parent','=',$product->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
	        $t = \DB::table('members');
	        $result->userAdmin = ($t->select('distinct user_id')
	        ->where('parent_type','=','teams')
	        ->where('parent','=',$product->parent)
	        ->where('status','=','active')
	        ->where('user_id','=',$user->id)
	        ->where('rank','=','admin')
	        ->count() > 0);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$product->parent)
        ->where('status','=','actevaluations.parentive')
        ->count();
    }
    
    
    public static function getInfo($product) {

		$result = JSON_decode('{
			"userLiked":false,
			"userDisLiked":false,
			"likeCount":0,
			"likeReq":0,
			"disLikeCount":0,
			"disLikeReq":0,
         "memberCount":0,
         "commentCount":0,
         "userAdmin":false,
         "usedCount":0,
         "evaulation":0,
         "userUsed":false
		}');
		Product::getLikeInfo($result, $product);
		$recs = \DB::select('select sum(quantity) quantity
		from orderitems
		where product_id = '.$product->id.' and
		status = "success"');
		if (count($recs) > 0) {
			$result->usedCount = $recs[0]->quantity;		
		}		
		$recs = \DB::select('select avg(value) value
		from evaluations
		where parent = '.$product->id.' and parent_type = "products"');
		if (count($recs) > 0) {
			$result->evaulation = $recs[0]->value;		
		}		
		if (!$result->evaulation) {
			$result->evaulation = 0;
		}
		if (!$result->usedCount) {
			$result->usedCount = 0;
		}
		$result->commentCount = \DB::table('messages')
			->where('parent_type','=','products')
			->where('parent','=',$product->id)
			->count();
			
		$user = \Auth::user();
		if ($user) {	
			$result->userUsed = (\DB::table('orderitems')
				->leftJoin('orders','orders.id','orderitems.order_id')
				->where('orderitems.product_id','=',$product->id)
				->where('orderitems.status','=','closed2')
				->where('orders.customer_type','=','users')
				->where('orders.customer','=',$user->id)
				->count() > 0);
			if (!$result->userUsed) {
			$result->userUsed = (\DB::table('orderitems')
				->leftJoin('orders','orders.id','orderitems.order_id')
				->leftJoin('members','members.parent','orders.customer')
				->where('members.parent_type','=','orders.customer_type')
				->where('orderitems.product_id','=',$product->id)
				->where('orders.customer_type','=','teams')
				->where('orderitems.status','=','closed2')
				->where('members.user_id','=',$user->id)
				->where('members.status','=','admin')
			    ->count() > 0);
			}	
		}		
		return $result;
    }
    
	 /**
	 * categorák lekérdezése az adatbázisból
	 * @param int $productId
	 * @return string szám.szám,...
	 */	
	 public static function getCategories($productId): string {
	 	if ($productId == 0) {
			return '';	 	
	 	}
	 	$recs = \DB::table('productcats')
	 		->where('product_id','=',$productId)
	 		->orderBy('category')
	 		->get();
		$categories = [];
		foreach ($recs as $rec) {
			$categories[] = $rec->category;		
		} 	 		
		return implode(',',$categories);
	 }    
	 
	 /**
	 * categories tárolása
	 * @param int $productId
	 * @param string $categories 'szám,szám,...'
	 * @return string  errorInfo vagy üres
	 */	
	 public static function saveCategories($productId, $categories): string {
		$errorInfo = '';
		try {
		 	\DB::table('productcats')
		 		->where('product_id','=',$productId)
	 			->delete();
	 		$w = explode(',',$categories);
	 		asort($w);
	 		foreach ($w as $category) {
				if ($category != '') {
				 	\DB::table('productcats')
				 	->insert([
				 	"product_id" => $productId,
				 	"category" => $category
				 	]);
				}	 		
	 		}	
		} catch (\Illuminate\Database\QueryException $exception) {
		    $errorInfo = $exception->errorInfo;
		}
		return $errorInfo;
	 } 
    
    /**
    * adat lekérés a listához team szerint
    * @param int $teamId
    * @param array $orderArr [fevaluations.parentieldname, 'asc|desc']
    * @param string $search
    * @param string $categories 'catId, catId,...'
    * @param bool $userAdmin
    * @param int $page
    * @param int $perPage   sorok száma egy oldalon
    * @return {items:[..], currentPage, offset, total, perPage}
    */
	 public static function getData($teamId,	$orderArr, 
			$search,	$categories, $userAdmin, $page, $perPage
		) {
		$user = \Auth::user();
		if ($user) {
			$userId = $user->id;
		} else {
			$userId = 0;
		}
		$sql = '
		select products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status,
		       avg(evaluations.value) value, products.parent_type, products.parent
		from products       
		left outer join evaluations on evaluations.parent = products.id       
		left outer join productcats on productcats.product_id = products.id
		where evaluations.parent_type = "products" ';
		if (!$userAdmin) {
			$sql .= ' and (products.status = "active" or (products.parent_type = "users" and products.parent = '.$userId.'))';		
		}
		if ($search != '') {
			$sql .= ' and products.name like "%'.$search.'%" ';
		}
		if ($categories != '') {	
			$sql .= ' and productcats.category in ('.$categories.') ';
		}	  
		if ($teamId > 0) {
		 	$sql .= ' and products.parent = '.$teamId.' and products.parent_type = "teams" ';
		}      
		$sql .= '
		group by products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status 
		order by '.$orderArr[0].' '.$orderArr[1];
		
		$data = new \stdClass();
		$data->items = \DB::select($sql);
		$data->currentPage = $page;
		$data->total = count($data->items);
		$data->perPage = $perPage;
		$data->offset = (($data->currentPage - 1) * $data->perPage);
		return $data;
   }
    
    
    /**
    * adat lekérés a listáhozevaluations.parent user szerint
    * @param int $userI
    * @param array $orderArr [fieldname, 'asc|desc']
    * @param string $search
    * @param string $categories 'catId, catId,...'
    * @param bool $userAdmin
    * @param int $page
    * @param int $perPage   sorok száma egy oldalon
    * @return {items:[..], currentPage, offset, total, perPage}
    */
	 public static function getDataByUser($userId,	$orderArr, 
			$search,	$categories, $userAdmin, $page, $perPage
		) {
		$sql = '
		select products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status,
		       avg(evaluations.value) value, products.parent_type, products.parent
		from products       
		left outer join evaluations on evaluations.parent = products.id       
		left outer join productcats on productcats.product_id = products.id
		where evaluations.parent_type = "products" ';
		if (!$userAdmin) {
			$sql .= ' and (products.status = "active" or (products.parent_type = "users" and products.parent = '.$userId.'))';		
		}
		if ($search != '') {
			$sql .= ' and products.name like "%'.$search.'%" ';
		}
		if ($categories != '') {	
			$sql .= ' and productcats.category in ('.$categories.') ';
		}	  
		if ($userId > 0) {
		 	$sql .= ' and products.parent = '.$userId.' and products.parent_type = "users" ';
		}      
		$sql .= '
		group by products.id, products.name, products.avatar, products.price,
			   products.stock, products.unit, products.status 
		order by '.$orderArr[0].' '.$orderArr[1];
		
		$data = new \stdClass();
		$data->items = \DB::select($sql);
		$data->currentPage = $page;
		$data->total = count($data->items);
		$data->perPage = $perPage;
		$data->offset = (($data->currentPage - 1) * $data->perPage);
		return $data;
   }
    
    /**
     * like/dilike ellenörzés, ha szülséges status modosítás
     * @param string $teamId
     * @return void
     */
    public static function checkStatus(string $teamId):void {
    }
    
    public static function getStock($product) {
		$result = 0;
		$recs = \DB::select('select sum(quantity) quantity
		from productadds
		where product_id = '.$product->id.'
		');
		if (count($recs) > 0) {
			$result = $result + $recs[0]->quantity;
		}
		$recs = \DB::select('select sum(quantity) quantity
		from orderitems
		where product_id = '.$product->id.' and
		status in ("ordering","confirme","closed1","closed2") 
		');
		if (count($recs) > 0) {
			$result = $result - $recs[0]->quantity;
		}
		return $result;
	}
	
	/**
	* bejelentkezett user admin a product.parent team -ben vagy ő a product.parent?
	* @param Product $product
	* @return bool
	*/
	public static function userAdmin($product): bool {
	    return \App\Models\Member::userAdmin('teams', $product->parent);
	}
	
	public function valid(Request $request):bool {		
		// tartalmi ellenörzések 
		$request->validate([
				'name' => 'required',
				'parent' => 'required',
				'description' => 'required',
				'unit' => 'required',
				'price' => ['required','numeric','min:0'],
				'currency' => 'required'
		]);
		return true;
	}		


	 /**
	 * product rekord irása az adatbázisba a $request-be lévő információkból
	 * @param int $id
	 * @param Request $request
	 * @return string, $id created new record id
	 */	 
	 public static function saveOrStore(int &$id, Request $request): string {	
			
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
			$fileInfo = Minimarkdown::getRemoteFileInfo($productArr['avatar']);
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

		    // file upload kezelése
		    $uploadMsg = Upload::processUpload('img',
											   storage_path().'/products/'.$id.'/',
											   'avatar',
											   ['jpg','png','gif']);
		    if ($uploadMsg != 'no upload') {
				if (substr($uploadMsg,0,5) != 'ERROR') {
					$avatarUrl = str_replace(storage_path(),\URL::to('/storage'),$uploadMsg);
					\DB::table('products')
						->where('id','=',$id)
						->update(["avatar" => $avatarUrl]);
				} else {
					$errorInfo = $uploadMsg;
				}
			}
		    return $errorInfo;
	 }	

}


