<?php
include_once './views/common.php';
class CommentsView  extends CommonView  {

	public function browser(Params $p) {
	    $this->setTemplates($p,['commentadd']);
	    $this->echoHtmlPage('commentslist',$p, 'groups');
	}
	
}
?>

