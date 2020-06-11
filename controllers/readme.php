<?php
/**
 * Leírás megjelenités kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class ReadmeController extends CommonController {
    
    
    function __construct() {
        $this->cName = 'readme';
    }
    
    /**
     * Leírás megjelítése task
     * @param Request $request
     */
    public function show(Request $request) {
        $p = $this->init($request,[]);
	    $this->view->display($p);
	}
}
?>