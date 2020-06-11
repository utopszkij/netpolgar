<?php
include_once './views/common.php';
class WorkingView  extends CommonView  {
	/**
	* echo html page
	* @param object $p
	* @return void
	*/
	public function display($p) {
	    if (!isset($p->user)) {
	        $p->user = new stdClass();
	        $p->user->id = 0;
	        $p->user->nick = 'guest';
	        $p->user->avatar = 'https://www.gravatar.com/avatar';
	    }
	    $this->echoHtmlHead($p);
        ?>	
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:block; padding:20px;">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
            <div style="text-align:center; padding:20px;">
    			<h2 style="text-align:center">Sajnos ez a funkció még nem müködik. :( </h2>
            	<img src="images/working.jpg" style="height:400px" />
                <div style="text-align:left">
                	Params:<br />
	                <pre><code><?php  echo JSON_encode($p, JSON_PRETTY_PRINT); ?></code></pre>
                </div>
            </div>
	      </div><!-- #scope -->
		  <?php $this->echoFooter(); ?>
		  <?php $this->loadJavaScriptAngular('working',$p); ?>
        </body>
        </html>
        <?php 		
	}
}
?>

