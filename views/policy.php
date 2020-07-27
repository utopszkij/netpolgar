<?php
include_once './views/common.php';
class PolicyView  extends CommonView  {
	/**
	* echo html page
	* @param object $p
	* @return void
	*/
	public function display($p) {
	    $this->setTemplates($p,[]);
	    $this->echoHtmlPage('policy', $p);
	}
}
?>

