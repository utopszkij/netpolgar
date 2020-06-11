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
	    $request->set('sessionid','0');
	    $request->set('lng','hu');
	    $request->set('option','frontpage');
	    $data = $this->init($request,[]); 
		$this->view->display($data);
	}
}
?>