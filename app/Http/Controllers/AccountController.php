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
				$userMember = (\DB::table('members')
					->where('user_id','=',$user->id)
					->where('parent_type','=','teams')
					->whereIn('rank',['member','admin'])
					->where('status','=','active')
					->count() > 0);
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
				 "ballance" => $ballance
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
    
}
