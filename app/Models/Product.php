<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'team_id', 
        'avatar', 'price', 'currency', 'vat', 'type', 'stock', 'unit', 'status'
    ];
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"team_id":0,
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
	        ->where('parent','=',$product->team_id)
	        ->where('status','=','active')
	        ->where('user_id','=',$user->id)
	        ->where('rank','=','admin')
	        ->count() > 0);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$product->team_id)
        ->where('status','=','active')
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
         "evaulation":0
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
		where product_id = '.$product->id);
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
    * adat lekérés a listához
    * @param int $teamId
    * @param array $orderArr [fieldname, 'asc|desc']
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
		if ($teamId > 0) {
		 	$sql .= ' and products.team_id = '.$teamId.' ';
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
    public function checkStatus(string $teamId):void {
    }
}


