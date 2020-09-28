<?php
use PhpParser\Node\Expr\BinaryOp\Identical;

include_once './controllers/common.php';
class CommentsController extends CommonController {
    
    function __construct() {
        $this->cName = 'comment';
    }
    
    
    /**
     * comment képernyő rész kirajzolása (nem task hanem be inkludolt rutin)
     * @param string $type objektum tipus ('group', groupMember...)
     * @param int $id objektum Id (groupId, groupId.userId ,projectId, productId,...)
     * @param string $label szöveg a képernyőre
     * @param string $help szöveg a képernyőre
     */
    public function show(string $type, int $id, string $label) {
        global $REQUEST;
        $this->init($REQUEST,[]);
        ?>
        <div id="divComments">
        	<p class="commentTitle"><em class="fa fa-minus-square"></em>&nbsp;
        		<var><?php  echo txt($label); ?></var>
        	</p>
        	<div id="commentsPage">----- comment form --------</div>
        </div>
        <?php 
	}
	
	/**
	 * comment lista
	 * @param Request $request - type, id, session loggedUser, commentOffsets
	 *    commentOffsets [{id, offset},....]  ha id>0 "id" alatti válaszzok offsetje
	 *                                        ha id=0 a legfelső szint offsetje
	 *    offset szám vagy "new" ez utobbi azt jelenti az első olvasatlantól vagy a legújabb 5                                          
	 */
	public function browser(Request $request, array $options=[]) {
	    $p = $this->init($request,[]);
	    $this->createCsrToken($request, $p);
	    $this->view->browser($p);
	}
	
	public function list(Request $request) {
	    $this->browser($request,[]);
	}
}
?>