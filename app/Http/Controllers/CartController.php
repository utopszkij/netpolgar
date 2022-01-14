<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller {
    
	/**
	* add to cart 
	* @param Request (product_id, quantity)
	* @return laravel redirect
	*/
	public function add(Request $request) {
		$product_id = $request->input('product_id',0);
		$quantity = $request->input('quantity',0);
		$product = \DB::table('products')
			->where('id','=',$product_id)
			->first();
		if ($product) {	
			if (\Auth::user()) {
				// tárolás adatbázisba
				$errorInfo = Cart::add((int) $product_id, 
											  (float) $quantity, 
											  \Auth::user());
				if ($errorInfo == '') {
					$result = view('cart.afteradd',
						["teamId" => $product->team_id]);
				} else {
					$result = redirect()->to('/products/'.$product_id)
						->with('error',$errorInfo);
				}	
			} else {
				$result = redirect()->to('/login')->with('error',__('cart.mustLogin'));		
			}
		} else {
			$result = redirect()->to('/products/list/0')
				->with('error','product not found');
		}
		return $result;
	}    
    
	/**
	* bejelentkezett user kosarának mutatása
	* user akciók: vissza a termék listához, 
	*              tétel törlés, 
	*              megrendelés elküldése
	* @return laravel redirekt
	*/
   public function show() {
   	$user = \Auth::user();
   	if ($user) {
   		$items = Cart::getItems($user->id);
   		$totalPrice = 0;
   		foreach ($items as $item) {
   			$item->price = round($item->price * $item->quantity * 10) / 10;
				$totalPrice = $totalPrice + $item->price;   		
   		}
   		$result = view('cart.show',[
   			"items" => $items,
   			"totalPrice" => $totalPrice
   		]);
   	} else {
			$result = redirect()->to('/products/list/0');   	
   	}
   	return $result;
   }
   
	/**
	* bejelentkezett user nyitott megrendelés összes tételét törli
	* @return laravel redirect
	*/
   public function clear() {
		$user = \Auth::user();
		if ($user) {
			$order = \DB::table('orders')
			->where('user_id','=',$user->id)
			->where('status','=','open')
			->first();
			if ($order) {
				\DB::table('orderitems')
				->where('order_id','=',$order->id)
				->delete();
				$result = redirect()->to('/products/list/0')
				->with('success',__('cart.success_clear'));				
			} else {
				$result = redirect()->to('/products/list/0');
			}		
		} else {
			$result = redirect()->to('/products/list/0');
		} 
		return $result;  
   }
   
	/**
	* bejelentkezett user nyitott order adott orderitem rekord törlése
	* @return laravel redirect
	*/
	public function delete($itemId) {
		$user = \Auth::user();
		if ($user) {
	   	$orderItem = \DB::table('orderitems')
	   		->where('id','=',$itemId)
	   		->first();
	   	if ($orderItem) {
	   		\DB::table('orderitems')
	   		->where('id','=',$itemId)
	   		->delete();
	   		$result = redirect()->to('/carts/list')
	   			->with('success',__('cart.deleted'));
	   	
	   	} else {
	   		$result = redirect()->to('/products/list/0')
	   			->with('error','orderitem not found');
	   	}
   	} else {
	   	$result = redirect()->to('/products/list/0')
	   			->with('error','not logged');
   	}
   	return $result;	
	}   

	/**
	* privát üzenet és email küldése
	* @param User $user
	* @param string $mailName pl: '\App\Mail\valami'
	* @param mixed $data adat a mailname számára
	* @param string $txt privát message szövege (markdown)
	* @return string hibaüzenet vagy üres
	*/	
	protected function sendMsg($user,
		string $mailName,
		string $data,
		string $txt): string {
		$result = '';
		// privát üzenet és email az érintett team rendszergazdáknak
		$admins =  \DB::table('orderitems')
		// privát üzenet küldése
		\DB::table('messages')
		->insert([
				'parent_type' => 'users',					
				'parent' => $user->id,					
				'reply_to' => 0,					
				'user_id' => $user->id,					
				'value' => $txt					
		]);
		\Mail::to($user->email)
		 		->send(new $mailName ($data));						
		if (\Mail::failures()) {
		   $result = 'mail send error.';
		}
		return $result;
	}
   
   /**
   * bejelentkezett user megrendezés befejezése; status modositás, értesités küldés
   * a tema rendszergazdáknak
   * @return laravel redirekt
   */
   public function send() {
   	$user = \Auth::user();
   	if ($user) {
   		$order = \DB::table('orders')
   			->where('user_id','=',$user->id)
   			->where('status','=','open')
   			->first();
   		if ($order) {
		   	// order és orderitem status modositása,
   			\DB::table('orderitems')
   			->where('order_id','=',$order->id)
   			->update([
					'status' => 'ordering'   			
   			]);
   			\DB::table('orders')
   			->where('id','=',$order->id)
   			->update([
					'status' => 'ordering'   			
   			]);
		   	// privát üzenet és email az érintett team rendszergazdáknak
   			$admins =  \DB::table('orderitems')
   				->select('members.user_id','orderitems.id')
   				->leftJoin('products','products.id','orderitems.product_id')
   				->leftJoin('teams','teams.id','products.team_id')
   				->leftJoin('members','members.parent','teams.id')
   				->where('members.parent_type','=','teams')
   				->where('members.status','=','active')
   				->where('members.rank','=','admin')
   				->where('orderitems.order_id','=',$order->id)
   				->distinct()
   				->get();	
				foreach ($admins as $admin) {
					$this->adminUser = \DB::table('users')
						->where('id','=', $admin->user_id)
						->first();
	   			$error = $this->sendMsg($this->adminUser,
						'\App\Mail\OrderMail',
						$admin->id,
						'Megrendelés érkezett!'."\n".
						'megrendelés:'."\n".
						\URL::to('/orders/confirm/'.$admin->id)."\n"
					);
					if ($error != '') {
						echo $error; exit();					
					}	
				}
				$result = redirect()->to('/products/list/0');   			
   		} else {
				$result = redirect()->to('/products/list/0')
					->with('error','not open order');   	
   		}	
   	} else {
			$result = redirect()->to('/products/list/0');   	
   	}
   	return $result;
   } 
   
	/**
	* gyártó/forgalmazó visszaigazolja a megrendelést
	* @param int $orderItemId
	* @return laravel redirect
	*/
   public function confirm($orerItemId) {
   	
		return redirect()->to('/confirm');
		   	
   	$orderItem = \DB::table('orderitems')
   		->where('id','=',$orderItemId)
   		->first();
   	if ($orderItem) {
   		$order = \DB::table('orders')
   			->where('id','=',$orderItem->order_id)
   			->first();
   		if ($order) {
   			$product = \DB::table('products')
   				->where('id','=',$orderItem->product_id)
   				->first();
   			// képernyő amin a megrendelés látható
   			// lehetőségek:
   			// - üzenet küldés a usernek
   			// - orderitem status modositás + üzenet
   			// - vissza a termék listához	
   		}			
   	}
		$result = redirect()->to('/products/list/0');   	
   }
}
