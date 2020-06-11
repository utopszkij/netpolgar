<?php
include_once './controllers/common.php';
class AdomanyController extends CommonController {
    
    function __construct() {
        $this->cName = 'adomany';
    }
    
    public function show(RequestObject $request) {
        $p = $this->init($request,'adomany');
        $this->view->display($p);
    }
}
?>