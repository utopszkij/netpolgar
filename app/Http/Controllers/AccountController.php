<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller {
    
    public function list(string $actorType, int $actorId) {
		if (\Auth::check()) {
			$user = \Auth::user();
			$userMember = false;
			if ($actorType == 'teams') {
				$team = \DB::table('teams')->where('id','=',$actorId)
					->first();
				$userMember = Account::userMember($actorType, $actorId, 
					$user->id);
				$userAdmin = Account::userAdmin($actorType, $actorId, 
					$user->id);
			} else {
				$userAdmin = ($user->id == $actorId);
			}
			if ((($actorType == 'users') & ($user->id == $actorId)) |
			    (($actorType == 'teams') & ($userMember))) {
				$data = Account::getData($actorType, $actorId, 8);
				foreach ($data as $item) {
					if (($item->from_type == $item->target_type) &
						($item->from == $item->target)) {
						$item->value = 0;	
					}	
					if (($item->from == $actorId) & 
						($item->from_type == $actorType)) {
						if ($item->target_type != '') {
							$item->partner = \DB::table($item->target_type)
										->where('id','=',$item->target)
										->first();
						} else {
							$item->partner = JSON_decode('{"name" : ""}');
						}				
						$item->value = 0 - $item->value;
					} else {
						if ($item->from_type != '') {
							$item->partner = \DB::table($item->from_type)
										->where('id','=',$item->from)
										->first();
						} else {
							$item->partner = JSON_decode('{"name" : ""}');
						}				
					}
				}

				if ($actorType == 'users') {
					$title = $user->name;
				} else {
					$title = $team->name;
				}
				$ballance = Account::getBallance($actorType, $actorId);
				$result = view('account.list',
				["data" => $data,
				 "title" => $title,
				 "ballance" => $ballance,
				 "userAdmin" => $userAdmin,
				 "accountId" => ucFirst(substr($actorType,0,1)).$actorId
				])
				->with('i', (request()->input('page', 1) - 1) * 8);
	
			} else {
				$result = redirect()->to(\URL::to('/'))
				->with('error',__('account.accessDenied'));
			}	
		} else {
			$result = redirect()->to(\URL::to('/'))
				->with('error',__('account.accessDenied'));
		}
		return $result;
	}
	
	/**
	 * Átutalás képernyő
	 * @param string $accountId 'Uszám|Tszám')
	 * @return laravel view|redirect
	 */ 
	public function send(string $accountId) {
		if (\Auth::check()) {
			$user = \Auth::user();
			$userMember = false;
			if (substr($accountId,0,1) == 'U') {
				$actorType = 'users';
			} else {
				$actorType = 'teams';
			}
			$actorId = substr($accountId,1,100);
			if ($actorType == 'teams') {
				$userAdmin = Account::userAdmin($actorType, $actorId, 
					$user->id);
			} else {
				$userAdmin = ($user->id == $actorId);
			}
			if ($userAdmin) {
				$result = view('account.send',[
				"fromType" => $actorType,
				"fromId" => $actorId,
				"fromTitle" => $accountId,
				"backUrl" => \URL::previous()
				]);
			} else {
				$result = redirect()->to(\URL::to('/'))
					->with('error',__('account.accessDenied'));
			}
		} else {
			$result = redirect()->to(\URL::to('/'))
				->with('error',__('account.accessDenied'));
		}
		return $result;	
	}
	
	/* Utalás képernyő végrehajtása
	 * @param Request ($fromType, $fromId, $tagetId, $value, comment) 
	 * @return laravel redirect
	 */ 
	public function dosend(Request $request) {
		if (\Auth::check()) {
			$user = \Auth::user();
			$fromType = $request->input('fromType');
			$fromId = $request->input('fromId');
			$targetId = $request->input('targetId');
			$value = $request->input('value',0);
			$comment = strip_tags($request->input('comment',''));
			if (substr($targetId,0,1) == 'U') {
				$tagetType = 'users';
			} else {
				$tagetType = 'teams';
			}
			$targetId = substr($tagetId,1,100);
			$backUrl = $request->input('backUrl','/');
			if ($fromType == 'teams') {
				$userAdmin = Account::userAdmin($fromType, $fromId, 
					$user->id);
			} else {
				$userAdmin = ($user->id == $actorId);
			}
			if ($userAdmin) {
				// egyenleg és value ellenörzés
				$valueOk = ($value > 0);
				if ($valueOk) {
					// $valueOk = Account::ballanceCheck($value);
				}
				// target létező számla?
				$t = \DB::table($targetType)
					->where('id','=',$targetId)
					->first();
				if ($t & $valueOk) {
					// rekord generálás
					$model = new Account();
					$model->create([
					"from_type" => $fromType,
					"from" => $fromId,
					"target_type" => $tagetType,
					"taget" => $targetId,
					"value" => $request->input('value',0),
					"comment" => $request->input('comment',''),
					"info" => 'send'
					]);
					$result = redirect()->to($backUrl)
						->with('success',__('account.successSend'));
				} else if (!$ŧ) {
					$result = redirect()->to(\URL::to($backUrl))
					->with('error',__('account.targetNotFound'));
				} else if ($value < 0) {
					$result = redirect()->to(\URL::to($backUrl))
					->with('error',__('account.valueError'));
				} else {
					$result = redirect()->to(\URL::to($backUrl))
					->with('error',__('account.ballanceError'));
				}
			} else {
				$result = redirect()->to(\URL::to('/'))
					->with('error',__('account.accessDenied'));
			}
		} else {
			$result = redirect()->to(\URL::to('/'))
				->with('error',__('account.accessDenied'));
		}
		return $result;	
	}
}
