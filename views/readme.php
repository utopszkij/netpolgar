<?php
/**
 * Leírás megjelenítés viewer
 */
include_once './views/common.php';

/** Leírás megjelenités osztály */
class ReadmeView  extends CommonView  {
	/**
	* echo html page
	* @param Params $p
	* @return void
	*/
	public function display(Params $p) {
	    global $REQUEST;
	    $lng = $REQUEST->sessionGet('lng','hu');
	    $htmlName = 'readme';
	    if (file_exists('langs/'.$htmlName.'_'.$lng.'.html')) {
	        $p->filePath = './langs/'.$htmlName.'_'.$lng.'.html';
	    } else if (file_exists('langs/'.$htmlName.'.html')) {
	        $p->filePath = './langs/'.$htmlName.'.html';
	    } else {
	        echo '<p>'.$htmlName.' html file not found.</p>'; exit();
	    }
	    $this->setTemplates($p,[]);
	    $this->echoHtmlPage('readme',$p);
	}
	
}
?>

