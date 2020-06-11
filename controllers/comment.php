<?php
use PhpParser\Node\Expr\BinaryOp\Identical;

include_once './controllers/common.php';
class CommentController extends CommonController {
    
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
}
?>