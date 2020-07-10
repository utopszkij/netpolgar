<?php
use PhpParser\Node\Expr\BinaryOp\Identical;

include_once './controllers/common.php';
class LikesController extends CommonController {
    
    function __construct() {
        $this->cName = 'like';
    }
    
	
	/**
	 * AJAX backend  - up ikonra kattintás
	 * @param Request $request - type, Identical
	 *    sessionban bejelentkezett user, userMember
	 */
	public function setlike(Request $request) {
	    $p = $this->init($request,[]);
	    $this->model->saveLike($p->type, $p->id, $p->loggedUser->id, 'like');
	    // $type, $id, state autoUpdate like counts alapjám
	    $model = $this->getModel($p->type);
	    $model->autoUpdate($p->id);
	    $result = $this->model->getCounts($p->type, $p->id);
	    $obj = $model->getRecord($p->id);
	    $result->state = $obj->state;
	    echo JSON_encode($result);
	}
	
	/**
	 * AJAX backend  - down ikonra kattintás
	 * @param Request $request - type, Identical
	 *    sessionban bejelentkezett user, userMember
	 */
	public function setdislike(Request $request) {
	    $p = $this->init($request,[]);
	    $this->model->saveLike($p->type, $p->id, $p->loggedUser->id, 'dislike');
	    // $type, $id, state autoUpdate like counts alapjám
	    $model = $this->getModel($p->type);
	    $model->autoUpdate($p->id);
	    $result = $this->model->getCounts($p->type, $p->id);
	    $obj = $model->getRecord($p->id);
	    $result->state = $obj->state;
	    echo JSON_encode($result);
	}
		
}
?>