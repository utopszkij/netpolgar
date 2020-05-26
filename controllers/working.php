<?php
/**
 * kezdő lap megjelenítése kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class WorkingController extends CommonController {
    
    /**
     * készül képernyő megjelenítés task
     * @param Request $request
     */
	public function show(Request $request) {
	    $request->set('option','working');
	    $data = $this->init($request,[]); 
		$this->view->display($data);
	}
}
?>