<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Poll;

class OptionController extends Controller {

	 /**
	 * poll kiegészitő információk lekérése
	 * @param poll record $poll
	 * @return object {userMember, userAdmin}
	 */
	 protected function getInfo($poll) {
	 	$result = JSON_decode('{"userMember":false, "userAdmin":false}');
	 	$user = \Auth::user();
	 	if ($user) {
		 	$result->userMember = (\DB::table('members')
		 		->where('parent_type','=',$poll->parent_type)
		 		->where('parent','=',$poll->parent)
		 		->where('user_id','=',$user->id)
		 		->where('status','=','active')->count() > 0);
		 	$result->userAdmin = (\DB::table('members')
		 		->where('parent_type','=',$poll->parent_type)
		 		->where('parent','=',$poll->parent)
		 		->where('user_id','=',$user->id)
		 		->where('rank','=','admin')
		 		->where('status','=','active')->count() > 0);
		 	if ($user->id == $poll->created_by) {
				$result->userAdmin = true;		 	
		 	}	
	 	}	 
	 	return $result;
	 }

	 /**
	 * jogosultság ellenörzés
	 * @param poll record $poll
	 * @param object $info
	 * @return bool
	 */	
	 protected function checkAccessRight($action, $poll, $info):bool {
	 	$result = true;
	 	if (($poll->status != 'proposal') & ($poll->status != 'debate')) {
			$result = false;	 	
	 	}
		if ((!$info->userMember) & (!$info->userAdmin)) {
			$result = false;	 	
		}
	 	if (($poll->status == 'proposal') & (!$info->userAdmin)) {
			$result = false;	 	
	 	}
	 	if (($action == 'edit') & (!$info->userAdmin)) {
			$result = false;	 	
	 	}
	 	return $result;
	 }

	 /**
	 * új opció felvitel képernyő
	 * @param Poll $poll
	 * @return laravel redirect vagy view
	 */
    public function create(Poll $poll) {
 		$poll->config = JSON_decode($poll->config);
  			$parent = \DB::table($poll->parent_type)
  			->where('id','=',$poll->parent)->first();
		if ($parent) {
			$info = $this->getInfo($poll);
			if ($this->checkAccessRight('add',$poll, $info)) {
				$model = new \App\Models\Option();
 				$options = $model->where('poll_id','=',$poll->id)
 				->orderBy('name')
 				->get();
 				$result = view('option.create',[
 					'parent' => $parent,
 					'poll' => $poll,
 					'options' => $options,
 					'backUrl' => \URL::previous()
 				]);
			} else {
				$result = redirect()->back()
				->with('error',__('poll.accessDenied'));    	
			}			
		} else {
			$result = redirect()->back()
			->with('error','fatal error parent not found');    	
		}
		return $result;
	}	

	/**
	* opció modosítás képernyő
	* @param string $optionId
	* @return laravel redirect vagy view
	*/
   public function edit(Option $option) {
		$poll = \App\Models\Poll::where('id','=', $option->poll_id);
		if ($poll) {
			$poll->config = JSON_decode($poll->config);
  			$parent = \DB::table($poll->parent_type)
  			->where('id','=',$poll->parent)->first();
			if ($parent) {
				$info = $this->getInfo($poll);
				if ($this->checkAccessRight('edit',$poll, $info)) {
    				$result = view('option.edit',[
    					'parent' => $parent,
    					'poll' => $poll,
    					'option' => $option,
    					'backUrl' => \URL::previous()
    				]);
				} else {
					$result = redirect()->back()
					->with('error',__('poll.accessDenied'));    	
				}			
			} else {
				$result = redirect()->back()
				->with('error','fatal error parent not found');    	
			}   			
		} else {
			$result = redirect()->back()
			->with('error','fatal error poll not found');    	
		}
   	return $result;
   }
    
	/**
	* új opció tárolása
	* @param Request (pollId, name, backUrl)
	* @return laravel redirect
	*/    
    public function store(Request $request) {
    	$poll = \App\Models\Poll::where('id','=',$request->input('pollId'))->first();
    	if ($poll) {
    		$poll->config = JSON_decode($poll->config);
  			$parent = \DB::table($poll->parent_type)
  			->where('id','=',$poll->parent)->first();
			if ($parent) {
				$info = $this->getInfo($poll);
				if ($this->checkAccessRight('add',$poll, $info)) {
					\App\Models\Option::create([
						'poll_id' => $request->input('pollId'),
						'name' => $request->input('name'),
						'decription' => '',
						'status' => 'proposal',
						'created_by' => \Auth::user()->id				
					]);
					$result = redirect()->to($request->input('backUrl'))
					->with('success',__('poll.saved'));    	
				} else {
					$result = redirect()->back()
					->with('error',__('poll.accessDenied'));    	
				}			
			} else {
				$result = redirect()->back()
				->with('error','fatal error parent not found');    	
			}
		} else {
			$result = redirect()->back()
			->with('error','fatal error parent not found');    	
		}
		return $result;
    }
    
	 /**
	 * opció modosítás tárolása
	 * @param Request (optionId, name, backUrl)
	 * @return laravel redirect
	 */
    public function update(Request $request) {
   	$option = \App\Models\Option::where('id','=', $request->input('optionId'))->first();
   	if ($option) {
   		$poll = \App\Models\Poll::where('id','=', $option->poll_id);
   		if ($poll) {
   			$poll->config = JSON_decode($poll->config);
	  			$parent = \DB::table($poll->parent_type)
	  			->where('id','=',$poll->parent)->first();
				if ($parent) {
					$info = $this->getInfo($poll);
					if ($this->checkAccessRight('edit',$poll, $info)) {
						$model = new \App\Models\Option();
						$model->where('id','=',$request->input('optionId'))
						->update([
							'name' => $request->input('name')
						]);
						$result = redirect()->to($request->input('backUrl'))
						->with('success',__('poll.saved'));    	
					} else {
						$result = redirect()->back()
						->with('error',__('poll.accessDenied'));    	
					}			
				} else {
					$result = redirect()->back()
					->with('error','fatal error parent not found');    	
				}   			
   		} else {
				$result = redirect()->back()
				->with('error','fatal error poll not found');    	
   		}
   	} else {
			$result = redirect()->back()
			->with('error','fatal error option not found');    	
   	}
   	return $result;
    }
    
    
}
