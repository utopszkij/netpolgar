<?php
/**
 * kezdő lap megjelenítése kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class WorkingController extends CommonController {
        
    function __construct() {
        $this->cName = 'working';
    }
    
    /**
     * készül képernyő megjelenítés task
     * @param Request $request
     */
	public function show(Request $request) {
	    $request->set('option','working');
	    $data = $this->init($request,[]);
	    $data->formTitle = 'Sajnos ez még nincs készen ..... dolgozunk rajata. :(';
		$this->view->display($data);
	}
}
?>