<?php
/**
 * Kezdőlap megjelenítés viewer
 */
include_once './views/common.php';

/** viewer osztály */
class FrontpageView  extends CommonView  {
	
    public function display(Params $p) {
        $this->setTemplates($p,['frontpage','navbar']);
        $this->echoHtmlPage('frontpage',$p);
    }

}
?>