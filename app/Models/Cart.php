<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

	/**
	* Új orderitems rekordot add a meglévő nyitott orderhez;
	* Ha nincs nyitott order akkor létrehozza
	* @param int $product_id
	* @param number $quantity
	* @param user record $user
	* @return string  errorInfo vagy üres
	*/
	public static function add(int $product_id, 
							   float $quantity, 
							   string $customerType,			
							   int $customer): string {
		$errorInfo = '';
		// vany nyitott order?
		$order = \DB::table('orders')
			->where('customer_type','=',$customerType)
			->where('customer','=',$customer)
			->where('status','=','open')
			->first();
		if (!$order) {
			// ha nincs akkor most létrehozzuk
			try {
				$order = \App\Models\Order::create([
					"customer_type" => $customerType,
					"customer" => $customer,
					"status" => "open",
					"description" => "",
					"address" => "",
					"shipping" => "",
					"confirminfo" => ""
				]);
			} catch (\Illuminate\Database\QueryException $exception) {
				$errorInfo = $exception->errorInfo;
			}	
		}
		if ($errorInfo == '') {
			try {
				$orderItem = \DB::table('orderitems')
				->insert([
					"order_id" => $order->id,
					"product_id" => $product_id,
					"quantity" => $quantity,
					"status" => "open",
					"confirminfo" => "" 		
				]);	
			} catch (\Illuminate\Database\QueryException $exception) {
	            $errorInfo = $exception->errorInfo;
	      }
      }
		return $errorInfo;
	}
	
	/**
	* kosár tételek lekérdezése
	* @param int $userId
	* @return array
	*/
	public static function getItems(string $customerType, int $customer) {
		$items = \DB::table('orderitems')
			->select('orderitems.id', 'orderitems.quantity',
				'products.name','products.avatar','products.description',
				'products.unit','products.price')
			->leftJoin('products','products.id','orderitems.product_id')
			->leftJoin('orders','orders.id','orderitems.order_id')
			->where('orders.customer_type','=',$customerType)
			->where('orders.customer','=',$customer)
			->where('orders.status','=','open')
			->orderBy('orderitems.id')
			->get();	
		return $items;	
	}
	
	/**
	 * csoportok ahol a bejelentkezett user admin
	 * @return array [{id,'teams', name},...]
	 */ 
	public static function getCustomerTypes() {
		$result = [];
		if (\Auth::check()) {
			$result = \DB::table('members')
			->select('teams.id as id','members.parent_type as type','teams.name as name')
			->leftJoin('teams','teams.id','members.parent')
			->where('members.user_id','=',\Auth::user()->id)
			->where('members.parent_type','=','teams')
			->where('members.status','=','active')
			->where('members.rank','=','admin')
			->where('teams.id','>',0)
			->orderBy('teams.name')
			->get();
		}
		return $result;
	}
}
	
