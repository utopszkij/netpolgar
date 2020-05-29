<?php
/**
 * user kezelés viewer
 */
include_once './core/browser.php';

/** user kezelés viewer osztály */
class MembersView  extends BrowserView  {

    
    public function browser(Params $p) {
        $this->browserForm($p);
    }
    
	
}
?>

