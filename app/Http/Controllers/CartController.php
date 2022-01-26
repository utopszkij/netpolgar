<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Account;

class CartController extends Controller {
    
	/**
	* add to cart 
	* @param Request (product_id, quantity)
	* @return laravel redirect
	*/
	public function add(Request $request) {
		$customerType = '';
		$customer = '';
		if (!\Auth::check()) {
			$result = redirect()->to('/login')->with('error',__('cart.mustLogin'));		
		} else if ($request->input('customerType','') != '') {
			// már kiválasztotta customerType -t
			$s = $request->input('customerType');
			$w = explode('_',$s);	// type_id
			$customerType = $w[0];
			$customer = $w[1];
			$request->session()->put('customerType',$s);
		} else {
			// most kell customerType -t választani
			$customerTypes = Cart::getCustomerTypes(); // [{ id, type, name },...]
			if (count($customerTypes) > 0) {
				$result = view('cart.customertypes',[
					"customerTypes" => $customerTypes,
					"customerType" => $request->session()->get('customerType',''),
					"product_id" => $request->input('product_id'),
					"quantity" => $request->input('quantity'),
					"nextUrl" => \URL::current()
				]);
			} else {
				$customerType = 'users';
				$customer = \Auth::user()->id;
			}
		}
		if ($customerType != '') {
			$product_id = $request->input('product_id',0);
			$quantity = $request->input('quantity',0);
			$product = \DB::table('products')
				->where('id','=',$product_id)
				->first();
			if ($product) {	
				// tárolás adatbázisba
				$errorInfo = Cart::add((int) $product_id, 
											  (float) $quantity, 
											  $customerType,
											  $customer);
				if ($errorInfo == '') {
					$result = view('cart.afteradd',
						["teamId" => $product->parent]);
				} else {
					$result = redirect()->to('/products/'.$product_id)
						->with('error',$errorInfo);
				}	
			} else {
				$result = redirect()->to('/products/list/0')
					->with('error','product not found');
			}
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
   public function show(Request $request) {
		$user = \Auth::user();
		if ($user) {
		$customerType = '';
		$customer = '';
		if (!\Auth::check()) {
			$result = redirect()->to('/login')->with('error',__('cart.mustLogin'));		
			} else if ($request->input('customerType','') != '') {
				// már kiválasztotta customerType -t
				$s = $request->input('customerType');
				$w = explode('_',$s);	// type_id
				$customerType = $w[0];
				$customer = $w[1];
				$request->session()->put('customerType',$s);
			} else {
				// most kell customerType -t választani
				$customerTypes = Cart::getCustomerTypes(); // [{ id, type, name },...]
				if (count($customerTypes) > 0) {
					$result = view('cart.customertypes',[
						"customerTypes" => $customerTypes,
						"customerType" => $request->session()->get('customerType',''),
						"product_id" => 0,
						"quantity" => 0,
						"nextUrl" => \URL::current()
					]);
				} else {
					$customerType = 'users';
					$customer = \Auth::user()->id;
				}
			}
			if ($customerType != '') {
				$items = Cart::getItems($customerType,$customer);
				$totalPrice = 0;
				$ballance = Account::getBallance($customerType, $customer);
				foreach ($items as $item) {
					$item->price = round($item->price * $item->quantity * 10) / 10;
						$totalPrice = $totalPrice + $item->price;   		
				}
				$customerRec = \DB::table($customerType)
					->where('id','=',$customer)
					->first();
				$result = view('cart.show',[
				    "customerType" => $customerType,
				    "customer" => $customer,
				    "customerName" => $customerRec->name,
					"items" => $items,
					"totalPrice" => $totalPrice,
					"ballance" => $ballance
				]);
			}	
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
			$s = \Request::session()->get('customerType','0_0');
			$w = explode('_',$s);
			$customerTpye = $w[0];
			$customerId = $w[1];
			$order = \DB::table('orders')
			->where('customer_type','=',$customerType)
			->where('customer','=',$customerId)
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
	   		->where('status','=','open')
	   		->first();
	   	if ($orderItem) {
	   		\DB::table('orderitems')
	   		->where('id','=',$itemId)
	   		->where('status','=','open')
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
   * NTC zárolás
   * @return laravel redirekt
   */
   public function send() {
	$s = \Request::session()->get('customerType','0_0');
	$w = explode('_',$s);
	$customerType = $w[0];
	$customerId = $w[1];
   	$user = \Auth::user();
   	
   	if ($user) {
   		$order = \DB::table('orders')
			->where('customer_type','=',$customerType)
   			->where('customer','=',$customerId)
   			->where('status','=','open')
   			->first();
   		if ($order) {
			if (!$this->checkNTCbalance($order, $customerType, $customerId)) {
				// nincs elég NTC -je!
				
			}
			if (!$this->allocNTCbalance($order, $customerType, $customerId)) {
				// NTC zárolás
				
			}
			
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
		   	// privát üzenet és email az érintett producereknek
		   	// producer: team
   			$admins =  \DB::table('orderitems')
   				->select('members.user_id','orderitems.id')
   				->leftJoin('products','products.id','orderitems.product_id')
   				->leftJoin('teams','teams.id','products.parent')
   				->leftJoin('members','members.parent','teams.id')
   				->where('products.parent_type','=','teams')
   				->where('members.parent_type','=','teams')
   				->where('members.status','=','active')
   				->where('members.rank','=','admin')
   				->where('orderitems.order_id','=',$order->id)
   				->distinct()
   				->get();
   			// producer: user
   			$admin =   \DB::table('orderitems')
   				->select('users.id as user_id','orderitems.id')
   				->leftJoin('products','products.id','orderitems.product_id')
   				->leftJoin('users','users.id','products.parent')
   				->where('products.parent_type','=','users')
   				->where('orderitems.order_id','=',$order->id)
   				->distinct()
   				->first();		
   			$admin[] = $admin;
   				
			foreach ($admins as $admin) {
					$this->adminUser = \DB::table('users')
						->where('id','=', $admin->user_id)
						->first();
	   			$error = $this->sendMsg($this->adminUser,
						'\App\Mail\OrderMail',
						$admin->id,
						'Megrendelés érkezett!'."\n".
						'megrendelés link:'."\n".
						\URL::to('/orders/'.$admin->id.'/confirm')
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
	 * kiszámolja, hogy az order mennyibe kerül,
	 * lekérdezi a customer egyenlegét
	 * return true ha van elég egyenlege
	 */ 
	protected function checkNTCbalance($order, $customerType, $customerId) {
		/* TEST egyenlőre nincs egyenleg ellenörzés
		$result = false;
		$total = 0;
		$items = \DB::table('orderitems')
		->select('orderitems.id', 'orderitems.quantity', 
			'products.parent_type', 'products.parent', 'products.price', 'products.name')
		->leftJoin('products','products.id','orderitems.product_id')
		->where('orderitems.id','=',$order->id)
		->where('orderitems.status','=','opening')
		->get();
		foreach ($items as $item) {
			$total = $total + (round($item->quantity * $item->price * 10) / 10);
		}	
		if ($total <= Account::getBalalnce($customerType, $customerId) - 1000) {
			$result = true;
		}
		*/
		$result = true; 
		return $result;		
	}


	/**
	 * zárolja az összeget (orderitem -enként egy sor, info -ba kerül $orderItemId)
	 * return true ha sikeres
	 * @param Order $order,
	 * @param string $customerType
	 * @param int $customerId
	 */ 
	protected function allocNTCbalance($order, $customerType, $customerId) {
		$items = \DB::table('orderitems')
		->select('orderitems.id', 'orderitems.quantity', 
			'products.parent_type', 'products.parent', 'products.price', 'products.name')
		->leftJoin('products','products.id','orderitems.product_id')
		->where('orderitems.order_id','=',$order->id)
		->where('orderitems.status','=','open')
		->get();
		foreach ($items as $item) {
			
			Account::create(["from_type" => $customerType,
			"from" => $customerId,
			"target_type" => $item->parent_type,
			"target" => $item->parent,
			"status" => "allocated",
			"value" => (round($item->quantity * $item->price * 10) / 10),
			"info" => "orderItem:".$item->id,
			"comment" => "Megrendelés azonosító:".$order->id. 
				' '.$item->name.' '.$item->quantity
			]);
		}
		return true;		
	}


   
 }
    
   
