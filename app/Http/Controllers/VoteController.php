<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Poll;

class VoteController extends Controller {

	protected function accessRight($action, $poll, $info): string {
		$result = '';
		if ($poll->status != 'vote') {
			$result = 'wrongPollStatus';		
		} else if ($info->userMember == false) {
			$result = 'accesDenied';		
		} else if ($info->userVoted) {
			$result = 'voteExists';		
		}		
		if ($result != '') {
			$result = __('vote.'.$result);
		}
		return $result;		
	}

	/**
	* szavazó képernyő
	* @param Poll $poll
	* @returm laravel view vagy redirect
	*/
	public function create(Poll $poll) {
		$model = new \App\Models\Poll();
		$parent = \DB::table($poll->parent_type)
			->where('id','=',$poll->parent)->first();
		$options = \DB::table('options')
						->where('poll_id','=',$poll->id)
						->where('status','=','active')
						->orderBy('name')
						->get();	
		if ($parent) {	
			$poll->config = JSON_decode($poll->config);		
			$info = $model->getInfo($poll, $parent);
			$errorInfo = $this->accessRight('create',$poll, $info);
			if ($errorInfo == '') {
				$result = view('vote.form',[
					'poll' => $poll,
					'options' => $options,
					'parent' => $parent				
				]);
			} else {
			$result = redirect()->back()
			->with('error',$errorInfo);
			}
		} else {
			$result = redirect()->back()
			->with('error','Fatal error parent not found');
		}
		return $result;
	}

	/**
	* leadott szavazat tárolása az adatbázisba, 
	* titkos szavazzásnál user_id törlése a ballots -ból
	* @param Request $request 
	* @param Poll	 $poll
	* @return string  hibaüzenet vagy üres
	*/
	protected function storeVote(Request $request, $poll):string {
		$result = '';
		$user = \Auth::user();
		$ballot = \DB::table('ballots')
			->where('poll_id','=',$poll->id)
			->where('user_id','=',$user->id)
			->first();	
		if ($ballot) {		
			$voteArr = ['poll_id' => $poll->id,
				'ballot_id' => $ballot->id,
				'accredited_id' => 0,
				'user_id' => 0
			];
			if (!$poll->config->secret) {
				$voteArr['user_id'] = $user->id;		
			}
			try {
				if (($poll->config->pollType == 'yesno') |
				    ($poll->config->pollType == 'onex')) {
					$voteArr['option_id'] = $request->input('vote');
					$voteArr['position'] =  1;
					\DB::table('votes')->insert($voteArr);
				}
				if ($poll->config->pollType == 'morex') {
					for ($p=0; $p<20; $p++) {
						if ($request->input('vote'.$p,'') != '') {
							$voteArr['option_id'] = $request->input('vote'.$p);
							$voteArr['position'] =  1;
							\DB::table('votes')->insert($voteArr);
						}			
					}
				}
				if ($poll->config->pollType == 'pref') {
					for ($p=0; $p<20; $p++) {
						if ($request->input('opt_'.$p,'') != '') {
							$voteArr['option_id'] = $request->input('opt_'.$p);
							$voteArr['position'] =  $request->input('pos_'.$p);
							\DB::table('votes')->insert($voteArr);
						}			
					}
				}
				
				if (($poll->config->secret) & ($result == '')) {
					\DB::table('ballots')
					->where('poll_id','=',$poll->id)
					->where('user_id','=',$user->id)
					->update(['user_id' => 0]);
				}
			} catch (\Illuminate\Database\QueryException $exception) {
			    $result = JSON_encode($exception->errorInfo);
				 \DB::table('votes')
				 	->where('ballot_id','=',$ballot->id)
				 	->delete();		    
			}	
		} else {
			$result = 'ballot not found';		
		}
		return $result;
	}
	
	/**
	* szavazat tárolása, jogosultság ellenörzés és tárolás
	* @param Request (pollId, optionId1, position1, optionId2, position2,...)
	* @return laravel redirect
	*/
	public function store(Request $request) {
		$user = \Auth::user();
		$model = new \App\Models\Poll();
		$poll = $model->where('id','=', $request->input('pollId'))->first();
		$ballot = \DB::table('ballots')
			->where('poll_id','=',$poll->id)
			->where('user_id','=',$user->id)
			->first();	
		if ($poll) {
			$poll->config = JSON_decode($poll->config);
			$parent = \DB::table($poll->parent_type)
				->where('id','=',$poll->parent)->first();
			if ($parent) {			
				$info = $model->getInfo($poll, $parent);
				$errorInfo = $this->accessRight('create',$poll, $info);
				if ($errorInfo == '') {
					$errorInfo = $this->storeVote($request,$poll);
				}
			} else {
				$errorInfo = 'parent not found';		
			}
		} else {
			$errorInfo = 'poll not found';		
		}
		if ($errorInfo == '') {
			$result = redirect()->to('/polls/'.$poll->id)
			->with('success',
			       'szavazat tárolva, szavazat ID='.(($ballot->id*24)+1435).
			       ' Ennek a számnak a segitségével bármikor ellenörizheti,
			         hogy szavazata helyesen szerepel az adatbázisban.
			         Ezt a számot csak Ön ismeri, tehát a szavazat titkossága
			         ezzel nem sérül.');		
		} else {
			$result = redirect()->to('/')->with('error',$errorInfo);		
		}
		return $result;
	}
	
	/**
	* leadott szavazatom ballot_id bekérő képernyő
	* @param Poll $poll
	*/
	public function getform(Poll $poll) {
		return view('vote.getform',["poll" => $poll]);
	}
	
	/**
	* leadott szavzat lekérdezése
	* @param Request (csr token poll_id, ballot_id)
	* @return laravel view vagy redirect
	*/
	public function show(Request $request) {
		$poll = \DB::table('polls')
		->where('id','=',$request->input('poll_id','0'))
		->first();
		$ballotId = $request->input('ballot_id',0);
		$ballotId = round(($ballotId - 1435 / 24));
		if  ($poll) {
			$poll->config = JSON_decode($poll->config);
			$votes = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->where('votes.ballot_id','=',$ballotId)
			->orderBy('votes.position')
			->get();
			$result = view('vote.show',[
				'poll' => $poll,
				'votes' => $votes,
				'backURL' => \URL::previous()			
			]);			
		} else {
			$result = redirect()->back()->with('error','poll not found');		
		}
		return $result;
	}

	/**
	* összes leadott szavazat lekérése
	* @param Poll $poll	
	*/
	public function list(Poll $poll) {
			$poll->config = JSON_decode($poll->config);
			$data = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->orderBy('votes.ballot_id','asc','votes.position','asc')
			->paginate(5);
			foreach ($data as $d1) {
				$d1->ballot_id = ($d1->ballot_id * 24) + 1435;			
			}
			if ($poll->config->pollType == 'yesno') {
				foreach ($data as $d1) {
					if ($d1->position == 1) {
						$d1->position = 'IGEN';
					} else {
						$d1->position = 'NEM';					
					}				
				}
			}
			if ($poll->config->pollType == 'onex') {
				foreach ($data as $d1) {
					$d1->position = '';
				}
			}
			if ($poll->config->pollType == 'morex') {
				foreach ($data as $d1) {
					$d1->position = '';
				}
			}
			if ($poll->config->pollType == 'pref') {
				foreach ($data as $d1) {
					$d1->position = $d1->position + 1;
				}
			}
			$result = view('vote.list',[
				'poll' => $poll,
				'data' => $data,
				'backURL' => \URL::previous()			
			]);
			return $result;			
	}
	
	/**
	* összes leadott szavazat csv formában
	*/
	public function csv(Poll $poll) {
			$poll->config = JSON_decode($poll->config);
			$data = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->orderBy('votes.ballot_id','asc','votes.position','asc')
			->get();
			foreach ($data as $d1) {
				$d1->ballot_id = ($d1->ballot_id * 24) + 1435;			
			}
			if ($poll->config->pollType == 'yesno') {
				foreach ($data as $d1) {
					if ($d1->position == 1) {
						$d1->position = 'IGEN';
					} else {
						$d1->position = 'NEM';					
					}				
				}
			}
			if ($poll->config->pollType == 'onex') {
				foreach ($data as $d1) {
					$d1->position = '';
				}
			}
			if ($poll->config->pollType == 'morex') {
				foreach ($data as $d1) {
					$d1->position = '';
				}
			}
			if ($poll->config->pollType == 'pref') {
				foreach ($data as $d1) {
					$d1->position = $d1->position + 1;
				}
			}
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="votes.csv"');
			foreach($data as $d1) {
				echo $d1->ballot_id.';'.$d1->position.';'.$d1->option_id.';"'.$d1->name.'"'."\n";			
			}
			exit();
	}
}
