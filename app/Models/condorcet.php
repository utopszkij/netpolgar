<?php

/*
  Schulze method implementation based on http://en.wikipedia.org/wiki/Schulze_method
  The test cases are from http://wiki.electorama.com/wiki/Schulze_method
  GNU GPL v3 or later
  (c) Árpád Magosányi 2013, sonar friend refactoring  Fogler Tibor 2018
  
	A '--a lejebb lévőket ellenzem--'  választási lehetőséget értelmezi  
  
*/

function echoHTMLTd($data, $class='') {
	return '<td class="'.$class.'">'.$data.'</td>'; 
}
function echoHTMLDiv($data, $class='') {
	return '<div class="'.$class.'">'.$data.'</div>'; 
}

/**
* condorcet - Shulze processing.
* use for database operands the abstract methods,
* without result report generator
*/
class CondorcetObj {
    protected $poll = null;  // poll id
    protected $candidates = array(); // key: candidate.id, value:candidate.name    
	 public $results = array(); // [[pos,név],...]	  
    protected $condorcetWinner = array();  // key: candidate.id, value: true|false
    protected $dMatrix = null;
    protected $pMatrix = null;
    protected $vote_count = 0;
	 protected $inFirst = array();   // key: candidate.id, value:number    
    protected $shortlist = array(); // value: candidate.id

   /**
	* @param integer Szavazás ID
	*/
   function __construct($pollId=0) {
          $this->poll = $pollId;
   }
    // ======================================
    // abstract methods
    // ======================================
    /* requested public funtions definitions in child object:
     *
     * getCandidates     database --> $this->candidates
     * @return $this->candidates 
     *
     *
     * loadDiffMatrix   database --> $this->dmatrix
     * @return $dMatrix   dMatrix[i,j]  
     * The 'i' candidate will prematurely precede the "j" candidate
     *      where "i" and "j" candidate.id
     *
     *
     * loadInFirst  database --> $this->inFirst
     * @return array $this->inFirst
     *    
	 * loadVoteCount   database --> $this->vote_count
     * @return integer  $this->vote_count
     *  
     */
   
	  // ============================
      //     standard methods
	  // ============================
      
		// sonar friend refactoring from floydWarshal2
		protected function flowWarshall3($i,$j) {				                  
           if($i != $j) {
              foreach($this->candidates as $k => $name3) {
                 if(($i != $k) && ($j != $k)) {
                    $this->pMatrix[$j][$k] = max($this->pMatrix[$j][$k], min ($this->pMatrix[$j][$i],$this->pMatrix[$i][$k]));
                 }
      	      }
           }
      }            

		/**
		* sonar friend refactoring from floydWarshall
		*/
      protected function flowdWarshall2() {
          foreach($this->candidates as $i => $name1) {
              foreach($this->candidates as $j => $name2) {
						$this->flowWarshall3($i,$j);
              }
          }
      }

      /**
      * Shulze method step 2.
      * $this->dMatrix -> $this->pMatrix
      * @return $this->pMatrix
      * use $this->candidates, $this->dMatrix
      */
      protected function floydWarshall() {
          $this->pMatrix = array();
          foreach($this->candidates as $i => $name1) {
              $this->pMatrix[$i] = array();
              foreach($this->candidates as $j => $name2) {
                  if($i != $j) {
                    if($this->dMatrix[$i][$j] > $this->dMatrix[$j][$i]) {
                      $this->pMatrix[$i][$j] = $this->dMatrix[$i][$j] ;
                    } else {
                      $this->pMatrix[$i][$j] = 0;
                    }
                  }
              }
          }
          $this->flowdWarshall2();
      }

      // support function for sort
      protected function beatsP($id1,$id2) {
          return  ($this->pMatrix[$id2][$id1] - $this->pMatrix[$id1][$id2]);
      }

      /**
      * calculate condorcet sort
      * @return void  set $this->shortlist candidates.id
      */    
      protected function findWinner() {
          $short_list = array_keys($this->candidates);
          usort($short_list,array('CondorcetObj','beatsP'));
          $this->shortlist = $short_list;
      }

}

/**
* condorcet - Shulze processing.
* use for database operands the abstract methods,
* include result report generator
*/
class Condorcet extends CondorcetObj {

		/**
		* print cell of matrix
		* @param array of array matrix
		* @param int $id1
		* @param  int $id2
		* @return string  '<td.......</td>'
		*/ 
		protected function printMatrixCell($matrix, $id1, $id2) {
         if ($id1 == $id2) {
            $result = '<td align="center"> - </td>';
         } else {
           if ($matrix[$id1][$id2] > $matrix[$id2][$id1]) {
              $class = 'green';
           } else if ($matrix[$id1][$id2] < $matrix[$id2][$id1]) {
              $class = 'red';
		  		  $this->condorcetWinner[$id1] = false;
           } else {
              $class = 'white';
           }   
           $result = echoHtmlTd($matrix[$id1][$id2], $class);
         } 
         return $result;   
		}            	

		/**
		* echo row of matrix
		* @param int number of row
		* @param arrray of array matrix
		* @param int $id1
		* @param string $name1
		* @return string <tr.......</tr>'
		*/
      protected function printMatrixRow($r, $matrix, $id1, $name1) {
        $result = '<tr>'.echoHtmlTd($r).echoHtmlTd($name1);
        foreach($this->candidates as $id2 => $name2) {
            if(array_key_exists($id1,$matrix) && array_key_exists($id2,$matrix[$id1])) {
					$result .= $this->printMatrixCell($matrix, $id1, $id2);            	
            } else {
               $result .= echoHtmlTd(' - ','self');
            }
        }
        return $result."</tr>\n";
      }

 
      /**
      * matrix --> html short by $this->candidates 
      * and set $this->condorcetWinner[$i]
      * @param matrix
      * @return string 
      */
      protected function printMatrix($matrix) {
          $result= '
          <table border="1" cellpadding="4" class="pollResult" width="100%">
          <tr><th>&nbsp;</th><th>&nbsp;</th>
          ';
          for ($c=0; $c < count($this->candidates); $c++) {
				$result .= '<th>'.($c+1).'</th>';          
          }	
          $result .= "</tr>";
          $r = 1;
          foreach($this->candidates as $id1 => $name1) {
          	  $result .= $this->printMatrixRow($r, $matrix, $id1, $name1);
              $r++;
          }
          return $result.'</table>'."\n";
      }

					 
		protected function showResultTableRow(&$pozition, $i, $j, $values, $accepted, &$trClass) {			 	
	       if ($j == 0) {
		           $pozition = 1;
	       } else if (($values[$i] < $values[$this->shortlist[$j-1]]) && (substr($this->candidates[$i],0,2) != '--')) {
		           $pozition++;
		    }       
	       $info = '';
	       if (($this->condorcetWinner1) & ($j==0)) {
	       	if (($values[$i] === $values[$this->shortlist[$j+1]])) {
			        $info .= '&nbsp;&nbsp;<strong>döntetlen</strong>';
	       	} else {
		         $info .= '&nbsp;-&nbsp;<strong style="color:orange">Condorcet gyöztes</strong>';
		      }    
	       }
	       if (($j > 0) && ($values[$i] === $values[$this->shortlist[$j-1]])) {
			        $info .= '&nbsp;&nbsp;<strong>döntetlen</strong>';
	       }   
			 if (substr($this->candidates[$i],0,2) == '--') {
				$trClass = 'eredmenySorEllenzett';
            $result = '<tr class="'.$trClass.'"><td colspan="5"><var class="noAccept">'.$this->candidates[$i].'</var></td></tr>';
			 } else {
            $result = '<tr class="'.$trClass.'">'.
            echoHtmlTd($pozition,'pozicio','pos').
            echoHtmlTd($values[$i],'shulze','').
            echoHtmlTd($this->candidates[$i].' '.$info,'nev').	
			   echoHtmlTd('&nbsp;'.$this->inFirst[$i].'&nbsp;&nbsp;&nbsp;'.Round($this->inFirst[$i] * 100 / $this->vote_count).'%','elso');
//			   echoHtmlTd('&nbsp;'.$accepted[$i].'&nbsp;&nbsp;&nbsp;'.Round($accepted[$i] * 100 / $this->vote_count).'%','elfodhato').'</tr>'."\n";	
          }
			 // $this->results[] = [$pozition, $this->candidates[$i]];
			 $this->results[] = [$values[$i], $this->candidates[$i]];
          return $result;
      }          

		/**
		* cretae resultTable HTML
		* @param array of candidatesId
		* @param array
		* @param array
		* @return string <table....../table>'
		*/
		protected function showResultTable($values, $accepted) {
          $result =  '<table class="pollResult" border="1" width="100%">
                     <tr>
                     	<th class="pos">Condorcet<br />helyezés</th>
                     	<th class="pos">Shulze eljárás <br />szerinti "súly"</th>
                     	<th class="nev">Név</th>
                     	<th class="elso">Első helyen szerepel</th>
                     	<th class="elfogadhato">Elfogadható</th>
                     </tr>'."\n";
		    $pozition = 0;
		    $trClass = 'eredmenySor';
		    $this->results = [];	
          foreach($this->shortlist as $j => $i) {
					 if (($i < count($this->inFirst)) &&  					 
						  (($this->inFirst[$i] == '')  |
							($this->inFirst[$i] == null) |
							($this->inFirst[$i] < 0)
						  )) { 
					 	$this->inFirst[$i] = 0;
					 }
					 $result .= $this->showResultTableRow($pozition, $i, $j, $values, $accepted, $trClass);
          }
          return $result."</table>\n";
	  }          
		
	  /**
	  * find notAcceped line in $this->candidates
	  * @return int
	  */	
     protected function findNotAccepted() {    
          $notAccept = 0;  
          foreach ($this->candidates as $i => $name) {
            if (substr($name,0,2) == '--') {
            	$notAccept = $i;
            }	
          }
          return $notAccept;  
     }   
		          
      /**
      * compute accepted numbers for candidates
      * @param int notAccepted candidates.ID
      * @return array
      */
      protected function computeAccepteds($notAccept) {    
          $accepted = array();  
          /*
          foreach ($this->candidates as $i => $name) {
            $accepted[$i] = $this->dMatrix[$i][$notAccept];
          }
          */
          return $accepted;            
      }    

     /**
     * compute Condorcet result values for candidates
     * @return array values
     */
     protected function computeValues() {    
        $values = array();
        $i = 0;
        $id1 = 0;
        $id2 = 0;
        $i = count($this->shortlist) - 1;
        $values[$this->shortlist[$i]] = 0;
        $lastValue = 0;
        for ($i=count($this->shortlist) - 2; $i >=0; $i--) {
            $id1 = $this->shortlist[$i];
            $id2 = $this->shortlist[$i+1];
            $values[$this->shortlist[$i]] = $lastValue + $this->pMatrix[$id1][$id2] - $this->pMatrix[$id2][$id1];
            $lastValue = $values[$this->shortlist[$i]];
         }
         return $values;
     }     

		/**
		*  resort $this->candidates and $accepted by $shortlist
		* @param array $accepted
		* @return void
		*/
		protected function showResultResort(&$accepted) {
		    $w = array();
		    $w1 = array();
		    /*
		    foreach ($this->shortlist as $i) {
				$w[$i] = $this->candidates[$i];
				$w1[$i] = $accepted[$i];
		    }
		    $this->candidates = $w;
		    */
		    $accepted = $w1;
		}

		/**
		*  check first is condorcet winner?
		* @return void   set $this->condorcetWinner1
		*/
		protected function showResultCheckCondorcetWinner() {	    
		    $i = $this->shortlist[0]; 
		    $this->condorcetWinner1 = true;
		    foreach  ($this->candidates as $j => $name) {
				if ($this->dMatrix[$i][$j] < $this->dMatrix[$j][$i]) {
					$this->condorcetWinner1 = false;
				}	
		    }
		}	    

	  /**
     * create condorcet result html  
     * @return string HTML string
	  */ 
      protected function showResult() {
		  if ($this->vote_count == 0) {
				$result = '<p class="nincsSzavazat">Nincs egyetlen szavazat sem.</p>';
		  } else  if (count($this->shortlist) == 0) {
            $result = '';
        } else { 
		  		$values = $this->computeValues($this->shortlist);
		  		$notAccepted = $this->findNotAccepted();	
		  		$accepted = $this->computeAccepteds($notAccepted);
		  		$this->showResultResort($accepted);	
		  		$this->showResultCheckCondorcetWinner();
				$result = $this->showResultTable($values, $accepted);
		  }	
		  return $result."\n";
      }

      /**
      * Full processs
      */
      public function report() {
        $this->getCandidates();
        $this->loadVoteCount();
        $this->loadInFirst();
        $this->loadDiffMatrix();
        $this->floydWarshall();
        $this->findWinner();
        return '<div class="condorcetResult">'."\n".
        echoHtmlDiv($this->showResult(), 'condorcetWinner')."\n".
        '<p><button type="button" class="btn btn-secondary" onclick="reszletekClick()">Részletek</button></p>'.
        '<div class="condorcetDetails" id="eredmenyInfo" style="display:none">'."\n".
        echoHtmlDiv('<h3>dMatrix</h3>'.$this->printMatrix($this->dMatrix),'dMatrix')."\n".
        '<p>A sorok és oszlopok is egy-egy opciót reprezentálnak. A sorban lévő opció ennyiszer elözte meg az oszlopban lévőt.</p>'.
        echoHtmlDiv('<h3>pMatrix</h3>'.$this->printMatrix($this->pMatrix),'pMatrix')."\n".
        '<p>A sorban lévő opció Shulze method szerinti "utvonal értéke" az oszlopban lévővel szemben.</p>'.
        '</div></div>'."\n";
      }
      
}

?>