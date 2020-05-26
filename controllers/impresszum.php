<?php
include_once './controllers/common.php';
class ImpresszumController extends CommonController {
	public function show(Request $request) {
	    $p = $this->init($request,['impresszum']);
	    $this->view->display($p);
	}
}
?>