<?php
include_once './controllers/common.php';
class ImpresszumController extends CommonController {
    
    function __construct() {
        $this->cName = 'impresszum';
    }
    
	public function show(Request $request) {
	    $p = $this->init($request,['impresszum']);
	    $p->formTitle = 'Impresszum';
	    $this->view->display($p);
	}
}
?>