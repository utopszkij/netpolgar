<?php
include_once './controllers/common.php';
class AdatkezelesController extends CommonController {
    
    function __construct() {
        $this->cName = 'adatkezeles';
    }
    
	public function show(Request $request) {
	    $p = $this->init($request,['adatkezeles']);
	    $this->view->display($p);
	}
}
?>