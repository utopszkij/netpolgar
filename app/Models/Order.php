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
	 * lapozható adat lekérés
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
     * user tagja a csoportnak?
     * @param int $teamId
     * @param int $userId
     * @return bool
     */ 
   	public static function userMember(int $teamId, int $userId):bool {			
		return (\DB::table('members')
				->where('parent_type','=','teams')
				->where('parent','=',$teamId)
				->whereIn('rank',['admin','member'])
				->where('status','=','active')
				->where('user_id','=',$userId)
				->count() > 0);
	}				

    /**
     * user adminja a csoportnak?
     * @param int $teamId
     * @param int $userId
     * @return bool
     */ 
   	public static function userAdmin(int $teamId, int $userId):bool {			
		return (\DB::table('members')
				->where('parent_type','=','teams')
				->where('parent','=',$teamId)
				->where('rank','=','admin')
				->where('status','=','active')
				->where('user_id','=',$userId)
				->count() > 0);
	}				

    
}
