<?php
 /**
  * condorcet algoritmus adatbázis interface
  */

class MyCondorcet extends Condorcet {

		/**
		* opciók meolvasása
		* @return $this->candidates [ $id => $name, ... ]
		*/
      protected function getCandidates() {
      	 $recs = \DB::table('options')
      	 ->where('poll_id','=',$this->poll)
      	 ->where('status','=','active')
      	 ->get();
      	 $this->candidates = [];
      	 foreach ($recs as $rec) {
      	 	$this->candidates[$rec->id] = $rec->name;
      	 }	
          return $this->candidates;
      }

		/**
		* difMatrix kialakítása
		* @return $this->dMatrix
		* kétdimenziós mátrix 
		*   [i][j] = a candidates[i] ennyiszer előzi a candidates[j] -t
		*/
		protected function loadDiffMatrix() {
     		 $this->dMatrix = [];
          $rows = \DB::select('
          	 select a.option_id as id1, b.option_id as id2, count(*) as d
             from votes a, votes b
             where  a.poll_id='.$this->poll.' and
                    b.poll_id=a.poll_id and
                    a.ballot_id=b.ballot_id and
                    a.position < b.position 
             		  group by a.option_id, b.option_id
             ');
          foreach($rows as $row ) {
              $id1 = $row->id1;
              $id2 = $row->id2;
              $d = $row->d;
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              $this->dMatrix[$id1][$id2] = $d;
          }
          foreach($this->candidates as $id1 => $name1) {
              if(!array_key_exists($id1,$this->dMatrix)) {
                  $this->dMatrix[$id1] = array();
              }
              foreach($this->candidates as $id2 => $name2) {
                  if(!array_key_exists($id2,$this->dMatrix[$id1])) {
                      $this->dMatrix[$id1][$id2] = 0;
                  }
              }
          }
          return $this->dMatrix;
      }

		/**
		* az egyes opciókat hányszor jelölték az első helyre
		* return $this->inFirst [ $option.id => darab, ...]
		*/
      protected function loadInFirst() {
        foreach($this->candidates as $id1 => $name1) {
            $this->inFirst[$id1] = 0;        
        }
        $res = \DB::select('
        select a.option_id, count(a.ballot_id) cc
        from votes a
        where a.poll_id = '.$this->poll.' and a.position = 0 
        group by a.option_id
        ');
        foreach ($res as $row) {
            $this->inFirst[$row->option_id] = $row->cc;
        }
        return $this->inFirst;
      }  

		/**
		* leadott szavazatok száma
		* @return $this->vote_count
		*/
      protected function loadVoteCount() {  
      	$res = \DB::select('
      	select DISTINCT a.ballot_id
 			from votes a
    		where a.poll_id = '.$this->poll);		
 	   	$this->vote_count = count($res);
      	return $this->vote_count;
      }  
} // myCondorcet
  
