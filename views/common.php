<?php
class CommonView extends View {
   
    /**
     * echo succes message after add new app
     * @param string $backLink
     * @param string $backStr
     * @param Params $p {user, userAdmin, avatarUrl,....}
     * @return void;}
     */
    public function successMsg(array $msgs,  string $backLink='', string $backStr='', Params $p) {
        $p->backStr = $backStr;
        $p->backLink = $backLink;
        $p->msgs = $msgs;
        $this->echoHtmlPage('successmsg', $p);
    }
    
	/**
	 * echo fatal error in app save
	 * @param array of string messages
	 * @param string backLink
	 * @param string backLinkText
	 * @param object $p {user, userAdmin, avatarUrl,....}
	 * @return void
	 */
	public function errorMsg(array $msgs, string $backLink='', string $backStr='', $p) {
	    $p->backStr = $backStr;
	    $p->backLink = $backLink;
	    $p->msgs = $msgs;
	    $this->echoHtmlPage('errormsg', $p);
	}
     
     /**
      * echo paginator
      * @param int $total
      * @param int $offset
      * @param int $limit
      */
     public function echoPaginator(int $total, int $offset, int $limit) {
         $offsetPrev = $offset - $limit;
         $offsetLast = 0;
         if ($offsetPrev < 0) {
             $offsetPrev = 0;
         }
         echo '<ul class="pagination">';
         echo '<li class="page-item disabled"><a class="page-link disabled">'.txt('TOTAL').': '.$total.' '.txt('PAGES').':</a></li>';
         if ($offset > 0) {
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick(0)">
                <em class="fa fa-backward" title="'.txt('FIRST').'"></em>
              </a></li>';
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetPrev.')">
                <em class="fa fa-caret-left" title="'.txt('PRIOR').'"></em></a></li>';
         }
         $p = 1;
         for ($o = 0; $o < $total; $o = $o + $limit) {
             if ($o == $offset) {
                 echo '<li class="page-item active"><a href=""  class="page-link disabled" onclick="false">'.$p.'</a></li>';
             } else {
                 echo '<li class="page-item"><a href="#"  class="page-link" onclick="paginatorClick('.$o.')">'.$p.'</a></li>';
             }
             $offsetLast = $o;
             $p = $p + 1;
         }
         $offsetNext = $offset + $limit;
         if ($offsetNext >= $offsetLast) {
             $offsetNext = $offsetLast;
         }
         if ($offset < $offsetLast) {
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetNext.')">
                <em class="fa fa-caret-right" title="'.txt('NEXT').'"></em></a></li>';
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetLast.')">
                <em class="fa fa-forward" title="'.txt('LAST').'"></em></a></li>';
         }
         echo '</ul>';
         echo '</div>';
     }
     
}
?>