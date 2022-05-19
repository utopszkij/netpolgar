<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $fillable = [
        'customer_type', 'customer', 'status', 'description',
        'address','shipping','confirminfo'
    ];
    
	/**
	 * lapozható adat lekérés producer vagy customer szerint
	 * @param string $producerType
	 * @param in $producerId
	 * @param string $customerType
	 * @param int $customerId
	 * @param int $pageSize
	 * @return paginator object		
	 */ 
	public static function getData(string $producerType, int $producerId, 
						string $customerType, int $customerId, 
						int $pageSize) {	
		$table = \DB::table('orderitems')
		->select('orderitems.id','orderitems.quantity', 'orderitems.created_at',
		'orderitems.status','products.unit','products.name',
		'products.parent_type','products.parent',
		'orders.id as orderId','orders.customer_type','orders.customer'
		)
		->leftJoin('orders','orders.id','orderitems.order_id')
		->leftJoin('products','products.id','orderitems.product_id')
		->where('orderitems.status','<>','open');
		
		if ($producerType != '') {
			$table = $table->where('products.parent_type','=',$producerType)
							->where('products.parent','=',$producerId);
		}
		if ($customerType != '') {
			$table = $table->where('orders.customer_type','=',$customerType)
							->where('orders.customer','=',$customerId);
		}
		return $table->orderby('orderitems.status','desc')
		->orderby('orderitems.created_at')
		->paginate($pageSize);
	}	
    
	/**
	 * lapozható adat lekérés készletmozgásokról product szerint
	 * (beleértve a készlet növeléseket is)
	 * a productadds rekordból származó készletnöveléseknél a
	 * "status" oszlopban a created_at adat szerepel
	 * @param in $productId
	 * @param int $pageSize
	 * @return paginator object		
	 */ 
	public static function getDataByProduct(int $productId, 
						int $pageSize) {	

		$table1 = \DB::table('productadds')
		->select('productadds.id','productadds.quantity', 'productadds.created_at',
		'productadds.created_at','products.unit','products.name',
		'productadds.id as orderId','productadds.user_id as customer_type','productadds.user_id as customer'
		)
		->leftJoin('products','products.id','productadds.product_id')
		->where('products.id','=',$productId);

		$table2 = \DB::table('orderitems')
		->select('orderitems.id','orderitems.quantity', 'orderitems.created_at',
		'orderitems.status','products.unit','products.name',
		'orders.id as orderId','orders.customer_type','orders.customer'
		)
		->leftJoin('orders','orders.id','orderitems.order_id')
		->leftJoin('products','products.id','orderitems.product_id')
		->whereIn('orderitems.status',['closed2','closed1'])
		->where('products.id','=',$productId)
		->union($table1);

		return $table2->orderby('created_at')
		->paginate($pageSize);
	}	
    
    
    /**
     * user tagja a csoportnak?
     * @param int $teamId
     * @param int $userId
     * @return bool
     */ 
   	public static function userMember(int $teamId, int $userId):bool {	
   	    return \App\Models\Member::userAdmin('teams', $teamId, $userId);
   	}				

    /**
     * user adminja a csoportnak?
     * @param int $teamId
     * @param int $userId
     * @return bool
     */ 
   	public static function userAdmin(int $teamId, int $userId):bool {			
   	    return \App\Models\Member::userAdmin('teams', $teamId, $userId);
	}				

    
}
