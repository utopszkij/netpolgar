<?php
/**
 * Kezdőlap megjelenítés viewer
 */
include_once './views/common.php';

/** viewer osztály */
class FrontpageView  extends CommonView  {
	
    public function display(Params $p) {
        $this->setTemplates($p,[]);
        $this->echoHtmlPage('frontpage',$p);
    }

}
?>