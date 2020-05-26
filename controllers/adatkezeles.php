<?php
include_once './controllers/common.php';
class AdatkezelesController extends CommonController {
	public function show(Request $request) {
	    $p = $this->init($request,['adatkezeles']);
	    $this->view->display($p);
	}
}
?>