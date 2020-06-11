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
	    $this->echoHtmlHead($p);
	    ?>
	    <body ng-app="app">
         <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
	    	<?php $this->echoNavbar($p); ?>
			<?php $this->echoLngHtml('readme',$p); ?>
			<?php $this->echoFooter(); ?>
			<?php $this->loadJavaScriptAngular('readme',$p); ?>
		  </div>	
	    </body>
	    </html>	
	    <?php 
	}
	
}
?>

