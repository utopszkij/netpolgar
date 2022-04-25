<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Poll;
use \App\Models\Vote;

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
		$parent = Vote::getParent($poll);
		$options = Vote::getOptions($poll);
		if ($parent) {	
			$poll->config = JSON_decode($poll->config);		
			$info = $model->getInfo($poll, $parent);
			$errorInfo = $this->accessRight('create',$poll, $info);
			if ($errorInfo == '') {

				// ha nincs hozzá ballot (később regisztrált) akkor most létrehozzuk...
				$ballot = \DB::table('ballots')
				->where('poll_id','=',$poll->id)
				->where('user_id','=',\Auth::user()->id)
				->first();
				if (!$ballot) {
					\DB::table('ballots')->insert(
						["poll_id" => $poll->id,
						 "user_id" => \Auth::user()->id]
					);
				}

				$result = view('vote.form',[
					'poll' => $poll,
					'options' => $options,
					'parent' => $parent				
				]);
			} else {
				$result = redirect()->to('/polls/'.$poll->id)->with('error',$errorInfo);
			}
		} else {
			$result = redirect()->to('/')->with('error','Fatal error parent not found');
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
		if (\Auth::check()) {
			$ballot = Vote::getBallot($poll, \Auth::user());
			if ($ballot) {	
				$result = Vote::store($request, $poll, $ballot);
			} else {
				$result = 'ballot not found';		
			}
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
		$ballot = Vote::getBallot($poll, $user);
		if ($poll) {
			$poll->config = JSON_decode($poll->config);
			$parent = Vote::getParent($poll);
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
			         ezzel nem sérül. FIGYELEM ha ezt a számot elfelejti, 
			         elveszti;soha többet nem kérdezhető le a programból,
			         hogy mi az ön szavazat azonsító száma!');		
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
		$ballotId = round(($ballotId - 1435) / 24);
		if  ($poll) {
			$poll->config = JSON_decode($poll->config);
			$votes = Vote::getVotes($poll, $ballotId);
			$result = view('vote.show',[
				'poll' => $poll,
				'votes' => $votes,
				'backURL' => \URL::previous()			
			]);			
		} else {
			$result = redirect()->to('/')->with('error','poll not found');		
		}
		return $result;
	}

	/**
	* összes leadott szavazat lekérése
	* @param Poll $poll	
	*/
	public function list(Poll $poll) {
			$poll->config = JSON_decode($poll->config);
			$data = Vote::getVotes($poll, 0, 5);
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
			$data = Vote::getVotes($poll,0,0);
		
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
			echo '"ballot_id";"position";"option_name"'."\n";
			foreach($data as $d1) {
				echo $d1->ballot_id.';'.$d1->position.';"'.$d1->name.'"'."\n";			
			}
			exit();
	}
}
