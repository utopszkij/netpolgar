<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Poll;

class OptionController extends Controller {

	 /**
	 * jogosultság ellenörzés
	 * @param Poll record $poll
	 * @param object $info
	 * @return bool
	 */	
	 protected function checkAccessRight(string $action, Poll $poll, $info):bool {
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
 		$parent = Option::getPollParent($poll);
		if ($parent) {
			$info = Option::getPollInfo($poll);
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
		$poll = \App\Models\Poll::where('id','=', $option->poll_id)
			->first();
		if ($poll) {
			$poll->config = JSON_decode($poll->config);
			$parent = Option::getPollParent($poll);
			if ($parent) {
				$info = Option::getPollInfo($poll);
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
			$parent = Option::getPollParent($poll);
			if ($parent) {
				$info = Option::getPollInfo($poll);
				if ($this->checkAccessRight('add',$poll, $info)) {
					$model = new Option();
					$errorInfo = $model->updateOrCreate($request);
					if ($errorInfo == '') {				
							$result = redirect()->to($request->input('backUrl'))
							->with('success',__('poll.saved'));    	
					} else {
							$result = redirect()->back()
							->with('error',$errorInfo);    	
					}	
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
    public function update(Request $request, $option) {
   	$option = \App\Models\Option::where('id','=', $request->input('optionId'))->first();
   	if ($option) {
   		$poll = \App\Models\Poll::where('id','=', $option->poll_id)->first();
   		if ($poll) {
   			$poll->config = JSON_decode($poll->config);
				$parent = Option::getPollParent($poll);
				if ($parent) {
					$info = Option::getPollInfo($poll);
					if ($this->checkAccessRight('edit',$poll, $info)) {
						$model = new Option();
						$errorInfo = $model->updateOrCreate($request);
						if ($errorInfo == '') {				
							$result = redirect()->to($request->input('backUrl'))
							->with('success',__('poll.saved'));    	
						} else {
							$result = redirect()->back()
							->with('error',$errorInfo);    	
						}	
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
