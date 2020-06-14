<?php
include_once './controllers/common.php';
class PolicyController extends CommonController {
    
    function __construct() {
        $this->cName = 'policy';
    }
    
	public function show(Request $request) {
	    $p = $this->init($request,[]);
	    $this->view->display($p);
	}
}
?>