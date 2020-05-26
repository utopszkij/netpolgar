<?php
include_once './controllers/common.php';
class AdomanyController extends CommonController {
    public function show(RequestObject $request) {
        $p = $this->init($request,'adomany');
        $this->view->display($p);
    }
}
?>