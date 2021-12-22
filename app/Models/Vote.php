<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
