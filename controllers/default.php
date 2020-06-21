<?php
/**
 * kezdő lap megjelenítése kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class DefaultController extends CommonController {
    
    /**
     * kezdőlap megjelenítés task
     * @param Request $request
     */
	public function default(Request $request) {
      // echo frontpage
	    // $request->set('sessionid','0');
	    $request->set('lng','hu');
	    $request->set('option','frontpage');
	    $p = $this->init($request,[]); 
	    $p->cookieEnabled = $request->sessionGet('cookieEnabled',false);
		$this->view->display($p);
	}
}
?>