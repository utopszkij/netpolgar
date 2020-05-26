<?php
/**
 * Leírás megjelenités kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class ReadmeController extends CommonController {
    
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