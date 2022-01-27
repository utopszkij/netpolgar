<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\OAccount;

class OrderController extends Controller {
	
	protected $userStatus = '';
	
	/**
	 * hozzáfési jog ellenörzése 
	 * (doConfirmnál ezen kivül további ellenörzés is szükséges)
	 * beállítja a $this->userStatus -t is
	 * @param string $action 'list'|'confirm'|'doconfirm'
	 * @param string $producerType
	 * @param int $producerId
	 * @param string $customerType
	 * @param int $customer
	 * @param User $user
	 * @return bool
	 */ 
	protected function accessCheck(string $action, 
		string $producerType, int $producerId,
		string $customerType, int $customerId,
		$user): bool {
		$result = false;	
		if ($action == 'list') {
			if (($producerType == 'users') & ($user->id == $producerId)) {
				$result = true;
			}	
			if ($producerType == 'teams') {
				$result = Order::userMember($producerId,$user->id);
			}
			if (($customerType == 'users') & ($user->id == $customerId)) {
				$result = true;
			}	
			if ($customerType == 'teams') {
				$result = Order::userMember($customerId, $user->id);
			}
			$this->userStatus = 'accessEnable';
		}	
		if (($action == 'confirm') | ($action == 'doconfirm')) {
			$this->userStatus = '';
			if (($producerType == 'users') & ($user->id == $producerId)) {
				$this->userStatus = 'producer';
			} else if (($producerType == 'teams')) {
				if ( Order::userAdmin($producerId, $user->id)) {
					$this->userStatus = 'producer';
				} else if ( Order::userMember($producerId, $user->id)) {
					$this->userStatus = 'producerMember';
				} 
			} else if (($customerType == 'users') & ($user->id == $customerId)) {	
				$this->userStatus = 'customer';
			} else if (($customerType == 'teams')) {
				if ( Order::userAdmin($customerId, $user->id)) {
					$this->userStatus = 'customer';
				} else if ( Order::userMember($customerId, $user->id)) {
					$this->userStatus = 'customerMember';
				}
			}	
			$result = ($this->userStatus != '');
		}	
		if ($action == 'doconfirm') {
			$result = (($this->userStatus == 'customer') | ($this->userStatus == 'producer'));
		}
		return $result;
	}

	/**
	 * user jogosult erre a státus változtatásra?
	 * @param Orderitem $orderitem,
	 * @param string $newStatus
	 * @param string $userStatus
	 * @return bool
	 */ 
	protected function accessCheck2($orderItem, $newStatus, $userStatus): bool {
		$result = false;
		$oldStatus = $orderItem->status;
		if ($userStatus == 'producer') {
			$result = ( (($oldStatus == 'ordering') & ($newStatus == 'confirmed')) |
						(($oldStatus == 'ordering') & ($newStatus == 'denied')) |
						(($oldStatus == 'confirmed') & ($newStatus == 'closed1')) |
						(($oldStatus == 'confirmed') & ($newStatus == 'denied'))
					  ); 
		}
		if ($userStatus == 'customer') {
			$result = ( (($oldStatus == 'ordering') & ($newStatus == 'canceled')) |
						(($oldStatus == 'confirmed') & ($newStatus == 'closed2')) |
						(($oldStatus == 'closed1') & ($newStatus == 'closed2'))
					  ); 
		}
		if ($oldStatus == $newStatus) {
			$result = true;
		}
		return $result;
	}
					
	/**
	 * orderItem -hez kiegészitő információk beolvasása
	 * @param Orderitem $orderItem
	 * @param Order $order
	 * @param Product $product
	 * @param User|Team $producer
	 * @param User|Team $customer
	 * @return string üres vagy hibajelzés
	 */ 
	protected function getInfo($orderItem, &$order, &$product, &$producer, &$customer) {
		$errorInfo = '';
		$order = \DB::table('orders')->where('id','=',$orderItem->order_id)
			->first();
		if (!$order) {
			$errorInfo = 'order not found';
		}	
		$product = \DB::table('products')->where('id','=',$orderItem->product_id)
			->first();
		if (!$product) {
			$errorInfo = 'product not found';
			$producer = false;
		} else {	
			$producer = \DB::table($product->parent_type)->where('id','=',$product->parent)
			->first();
		}
		if (!$producer) {
			$errorInfo = 'producer not found';
		}	
		$customer = \DB::table($order->customer_type)->where('id','=',$order->customer)
			->first();
		if (!$customer) {
			$errorInfo = 'customer not found';
		}	
		return $errorInfo;
	}


	/**
	 * megrendelés lista
	 * URL params: producer_type, producer, customer_type, customer, order
	 * @return laravel view
	 */ 
	public function list() {
		$producerType = \Request::input('producer_type','');
		$producerId = \Request::input('producer',0);
		$customerType = \Request::input('customer_type','');
		$customerId = \Request::input('customer',0);
		$user = \Auth::user();
		if (!$user) {
			return redirect()->to(\URL::to('/'))
			->with('error',__('order.accessDenied'));
		}


		$producer = new \stdClass();
		$customer = new \stdClass();

		$customer->name = '';
		$producer->name = '';
		if ($producerType != '') {
			$producer = \DB::table($producerType)->where('id','=',$producerId)->first();
		}
		
		if ($customerType != '') {
			$customer = \DB::table($customerType)->where('id','=',$customerId)->first();
		}

		$accessEnable = $this->accessCheck('list', 
											$producerType, $producerId,
											$customerType, $customerId,
											$user);
		if ($accessEnable) {
			$data = Order::getData($producerType, $producerId, 
						$customerType, $customerId, 8);
			if ($producerId > 0) {
				$title = __('order.received');
			} else {
				$title = __('order.sended');
			}
			return view('order.list',[
				"data" => $data,
				"producer" => $producer,
				"customer" => $customer,
				"title" => $title,
				"customerType" => $customerType,
				"customerId" => $customerId,
				"producerType" => $producerType,
				"producerId" => $producerId
			])
			->with('i', (request()->input('page', 1) - 1) * 8);;
		} else {
			return redirect()->to(\URL::to('/'))
			->with('error',__('order.accessDenied'));
		}
	}
	
	/**
	 * lapozható lista lekérés adott termékhez
	 * @param int $productId
	 * @return object
	 */
	public function listByProduct($productId) {
		$product = \DB::table('products')->where('id','=',$productId)
			->first();
		$data = Order::getDataByProduct($productId,8);
		// a készlet növeléseknél status == created_at -t ad a model
		foreach ($data as $item) {
			if ($item->status != $item->created_at) {
				$item->quantity = 0 - $item->quantity;
			}
		}	
		return view('order.listbyproduct',[
		"data" => $data,
		"product" => $product])
		->with('i', (request()->input('page', 1) - 1) * 8);
	}
	
	/**
	 * megrendelés visszaigazolás, megszakitás .... képernyő megjeleítő
	 * @param int $orderItemId
	 * @return laravel view | redirect
	 * customer user vagy customer team admin lehetőségei
	 * - ha status=='ordered' akkor status='canceled' modositás
	 * - ha status=='cloded1' akkor status='closed2' modositás
	 * - üzenet a producernek
	 * producer user vagy producer team admin lehetőségei
	 * - ha status=='ordered' akkor status="confirmed" modositás
	 * - ha status=='ordered' akkor status="canceled" modositás
	 * - ha status=='confirmed' akkor status="closed1" modositás
	 * - ha status=='confirmed' akkor status="canceled" modositás
	 * - üzenet a customernek
	 * customer team mebers lehetőségei
	 * - megtekintés
	 * producer team mebers lehetőségei
	 * - megtekintés
	 */ 
	public function confirm(int $orderItemId) {
		$errorInfo = '';
		$orderItem = Orderitem::where('id','=',$orderItemId)
			->first();
		$user = \Auth::user();
		$result = '';
		$order = false;
		$product = false;
		$producer = false;
		$customer = false;
		if (!$user) {
			$errorInfo = 'not logged';
		}
		if ($errorInfo == '') {
			$errorInfo = $this->getInfo($orderItem, $order, $product, 
				$producer, $customer);
		}
		if ($errorInfo == '') {
			// userstatust is beállítja
			$accessEnable = $this->accessCheck('confirm', 
								$product->parent_type, $producer->id,
								$order->customer_type, $customer->id,
								$user);

			if ($accessEnable) {
				$result = view('order.confirm',[
					"order" => $order,
					"orderItem" => $orderItem,
					"product" => $product,
					"producer" => $producer,
					"customer" => $customer,
					"userStatus" => $this->userStatus
				]);
			} else {
				$result = redirect()->to(\URL::to('/'))->with('error',__('order.accessDenied' ));	
			}	
		} else {
			$result = redirect()->to(\URL::to('/'))->with('error',$errorInfo);	
		}			
		return $result;				
	}
	
	/**
	 * confirm képernyőn megadott adatok tárolása
	 * @param Request (orderItemId, newStatus, msg)
	 * @return laravel redirect
	 */ 
	public function doConfirm(Request $request) {
		$result = '';
		$errorInfo = '';
		$user = \Auth::user();
		if (!$user) {
			$errorInfo = 'not logged';
		}
		$result = '';
		$order = false;
		$product = false;
		$producer = false;
		$customer = false;
		$orderItem = Orderitem::where('id','=',$request->input('orderItemId'))
			->first();
		if (!$orderItem) {
			$errorInfo = 'orderItem not found';
		}	
		if ($errorInfo == '') {
			$errorInfo = $this->getInfo($orderItem, $order, $product, 
				$producer, $customer);
		}
		if ($errorInfo == '') {
			// userstatust is beállítja
			$accessEnable = $this->accessCheck('doconfirm', 
								$product->parent_type, $producer->id,
								$order->customer_type, $customer->id,
								$user);
			if ($accessEnable) {
				$newStatus = $request->input('newStatus','');
				if ($this->accessCheck2($orderItem, $newStatus, 
					$this->userStatus)) {				
					// orderItem rekord modositása
					if ($newStatus != $orderItem->status) {
						if (!Orderitem::where('id','=',$orderItem->id) 
							->update(["status" => $newStatus])) {
								$errorInfo = 'SQL error(1)';
						}
						if (($orderItem->status != 'closed2') & 
							($newStatus == 'closed2')) {
							if (!$this->NTCtransver($order, $orderItem)) {
								$errorInfo = 'SQL error(2)';
							}
						}
						if (($orderItem->status != 'denied') & 
							($newStatus == 'denied')) {
							if (!$this->NTCunLock($order, $orderItem)) {
								$errorInfo = 'SQL error(3)';
							}
						}
						if (($orderItem->status != 'canceled') & 
							($newStatus == 'canceled')) {
							if (!$this->NTCunLock($order, $orderItem)) {
								$errorInfo = 'SQL error(4)';
							}
						}
						
						if (!$this->checkOrderStatus($orderItem->order_id)) {
								$errorInfo = 'SQL error(5)';
						}
					}
					if ($request->input('msg') != '') {
						$msg = strip_tags($request->input('msg'));
						if ($this->userStatus == 'customer') {
							$this->sendMsg($msg,$product->parent_type, 
								$product->parent);
						} else if ($this->userStatus == 'producer') {
							$this->sendMsg($msg,$order->customer_type, 
											$order->customer);
						}	
					}
					if ($this->userStatus == 'producer') {
						$result = redirect()->to(\URL::to('/orders/list/?producer_type='.$product->parent_type.'&producer='.$product->parent))
						->with('success',__('order.successConfirm'));
					} else {
						$result = redirect()->to(\URL::to('/orders/list/?customer_type='.$order->customer_type.'&customer='.$order->customer))
						->with('success',__('order.successConfirm'));
					}	
				} else {
					$result = redirectt()->to(\URL::to('/'))
						->with('error',__('order.accessDenied').'(1)');	
				}
			} else {
				$result = redirect()->to(\URL::to('/'))
					->with('error',__('order.accessDenied').'(2)');	
			}	
		} else {
			$result = redirect()->to(\URL::to('/'))->with('error',$errorInfo.'(6)');
		}	
		return $result;
	}
	
	/**
	 * ha az Order -nek nincs 'ordered', 'confirmed' és 'closed1' stáruszu tétele
	 * akkor modositja az order status -t 'closed' -re
	 * @param int $orderId
	 * @return bool true ha sikeres
	 */  
	protected function checkOrderStatus(int $orderId):bool {
		return true;
	}
	
	/**
	 * üzenet küldés
	 * @param string $msg
	 * @param string $targetType 'teams'|'users'
	 * @param int $targetId
	 * @return bool true ha sikeres
	 */ 
	protected function sendMsg(string $msg, string $targetType, int $targetId):bool {
		$user = \Auth::user();
		if ($targetType == 'users') {
			$target = \DB::table('users')->where('id','=',$targetId)->first();
			$targets = [$target];
		}
		if ($targetType == 'teams') {
			$targets = \DB::tables('members')
			->select('users.id, users.email')
			->leftJoin('users','users.id','=','members.user_id')
			->where('members.parent_type','=','teams')
			->where('members.parent','=',$tagetI)
			->where('members.rank','=','admin')
			->where('members.status','=','active')
			->get();
		}
		$msg = strip_tags($msg);
		foreach($targets as $target) {
			\DB::table('messages')
			->insert([
					'parent_type' => 'users',					
					'parent' => $target->id,					
					'reply_to' => 0,					
					'user_id' => $user->id,					
					'value' => $msg					
			]);
			\Mail::to($target->email)
					->send(new \App\Mail\ConfirmMail($user, $msg));						
			if (\Mail::failures()) {
			   echo 'mail send error. target:'.$target->email; exit();
			}
		}
		return true;
	}
		
	/**
	 * NTC zárolás véglegesítése ($orderItemId az info -ban van)
	 * @param Order $order
	 * @param Orderitem $orderItem
	 * @return bool  true ha sikeres
	 */ 	
	protected function NTCtransver($order, $orderItem) {
		Account::where('from_type','=',$order->customer_type)
			->where('from','=',$order->customer)
			->where('info','=','orderItem:'.$orderItem->id)
			->update(["status" => ""]);
		return true;
	}	

	/**
	 * NTC zárolás feloldása ($orderItemId az info -ban van)
	 * @param Order $order
	 * @param Orderitem $orderItem
	 * @return bool  true ha sikeres
	 */ 	
	protected function NTCunlock($order, $orderItem):bool {
		
		echo 'NTCunlock'.JSON_encode($order).JSON_encode($orderItem); 
		
		\DB::table('accounts')->where('from_type','=',$order->customer_type)
			->where('from','=',$order->customer)
			->where('info','=','orderItem:'.$orderItem->id)
			->delete();
		return true;
	}	
}	


