<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

include_once __DIR__.'/condorcet.php';
include_once __DIR__.'/mycondorcet.php';

class Vote extends Model {
    use HasFactory;
    
	 /**
	 * információk kiolvasása, (rész)eredmény számytás
	 * @param Poll $poll
	 * @return { data:[], labels:[], unit:'' }
	 */
    public function getInfo($poll) {
    	$result = JSON_decode('{"data":[], "labels":[], "unit":""}');
    	
    	if ($poll->config->pollType == 'yesno') {
    		 $cc = \DB::table('votes')
    				->where('poll_id','=',$poll->id)
    				->where('option_id','=',1)
    				->count();
    		$result->data[] = $cc;		
    		$result->labels[] = 'Igen '.$cc.' db';
    		
    		$cc = \DB::table('votes')
    				->where('poll_id','=',$poll->id)
    				->where('option_id','=',0)
    				->count();
    		$result->data[] = $cc;		
    		$result->labels[] = 'Nem '.$cc.' db';
    		$result->unit = 'db';
    	}
    	if (($poll->config->pollType == 'onex') |
    	    ($poll->config->pollType == 'morex')) {
			 $recs = \DB::select('select votes.option_id, options.name, count(*) cc
			 from votes
			 left outer join options on options.id = votes.option_id
			 where votes.poll_id = "'.$poll->id.'"
			 group by votes.option_id, options.name
			 order by 3 DESC
			 ');
			 foreach ($recs as $rec) {
				$result->data[] = (int)$rec->cc;
				$result->labels[] = $rec->name.' '.$rec->cc.' db';			 
			 }
			 $result->unit = 'db';
    	}
    	if ($poll->config->pollType == 'pref') {
    		 /* Ideiglenes átlag pozició alapú számítás
			 $recs = \DB::select('select votes.option_id, options.name, 
			    round(avg(votes.position)) cc
			 from votes
			 left outer join options on options.id = votes.option_id
			 where votes.poll_id = "'.$poll->id.'"
			 group by votes.option_id, options.name
			 order by 3
			 ');
			 $max = 0;
			 foreach ($recs as $rec) {
			 	if ($rec->cc > $max) {
					$max = $rec->cc;			 	
			 	}
			 }				 
			 foreach ($recs as $rec) {
				$result->data[] = $max + 1 - (int)$rec->cc;
				$result->labels[] = $rec->name.' átlag pozició:'.$rec->cc.'.';			 
			 }
			 $result->unit = 'súly';
    		 */
    		 	
    		 // condorcet -shulze metod szerinti számítás
    		 $myCondorcet = new \MyCondorcet($poll->id);
    		 $result->html = $myCondorcet->report();
    		 $result->data = [];
    		 $result->labels = [];
    		 $result->unit = 'súly';
    		 foreach ($myCondorcet->results as $res) {
				$result->data[] = $res[0]+0.2;
				$result->labels[] = $res[1];
    		 } 
    	}
    	return $result;
    }

	/**
	 * poll parent lekérdezése
	 * @param Poll $poll
	 * @ rezurn onject|false
	 */ 
   	public static function getParent($poll) {	
		return \DB::table($poll->parent_type)
			->where('id','=',$poll->parent)->first();
	}	

	/**
	 * poll opciók lekérdezése
	 * @param Poll $poll
	 * @return array
	 */ 
	public static function getOptions($poll) {	
		return \DB::table('options')
						->where('poll_id','=',$poll->id)
						->where('status','=','active')
						->orderBy('name')
						->get();	
	}	
	
	/**
	 * szavazat tárolása
	 * @param Request $request
	 * @param Poll $poll
	 * @param Ballot $ballot
	 * @return string üres vagy hibaüzenet
	 */ 
	public static function store($request, $poll, $ballot): string {			
		$result = '';
		$user = \Auth::user();
		if (!$user) {
			return 'not logged';
		}
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
		return $result;	
	}		
				
	/**
	 * ballot beolvasása
	 * @param Poll $poll
	 * @param User $user
	 */ 
	public static function getBallot($poll, $user) {	
		return \DB::table('ballots')
			->where('poll_id','=',$poll->id)
			->where('user_id','=',$user->id)
			->first();	
	}

	/**
	 * szavazatok lekérdezése
	 * @param Poll $poll
	 * @param int $ballotId  - lehet nulla is
	 * @param int $pageSize  - lehet nulla is
	 * @return array | paginator data
	 */ 			
	public static function getVotes($poll, $ballotId, $pageSize = 5) {		
		if ($ballotId > 0) {	
			$result = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->where('votes.ballot_id','=',$ballotId)
			->orderBy('votes.position')
			->get();
		} else if ($pageSize > 0) {
			$result = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->orderBy('votes.ballot_id','asc','votes.position','asc')
			->paginate($pageSize);
		} else {
			$result = \DB::table('votes')
			->leftJoin('options','options.id','votes.option_id')
			->where('votes.poll_id','=',$poll->id)
			->orderBy('votes.ballot_id','asc','votes.position','asc')
			->get();
		}	
		return $result;	
	}		


}
