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
										$user): string {
		$errorInfo = '';
		// vany nyitott order?
		$order = \DB::table('orders')
			->where('user_id','=',$user->id)
			->where('status','=','open')
			->first();
		if (!$order) {
			// ha nincs akkor most létrehozzuk
			try {
				\DB::table('orders')
				->insert([
					"user_id" => $user->id,
					"status" => "open",
					"description" => "",
					"address" => "",
					"shipping" => "",
					"confirminfo" => ""
				]);
				$order = \DB::table('orders')
					->where('user_id','=',$user->id)
					->where('status','=','open')
					->first();
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
					"status" => "opeen",
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
	public static function getItems(int $userId) {
		$items = \DB::table('orderitems')
			->select('orderitems.id', 'orderitems.quantity',
				'products.name','products.avatar','products.description',
				'products.unit','products.price')
			->leftJoin('products','products.id','orderitems.product_id')
			->leftJoin('orders','orders.id','orderitems.order_id')
			->where('orders.user_id','=',$userId)
			->where('orders.status','=','open')
			->orderBy('orderitems.id')
			->get();	
		return $items;	
	}
}
	
