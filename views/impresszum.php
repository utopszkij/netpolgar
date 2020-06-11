<?php
include_once './views/common.php';
class ImpresszumView  extends CommonView  {
	/**
	* echo html page
	* @param object $p
	* @return void
	*/
	public function display($p) {
	    $this->echoHtmlHead($p);
        ?>	
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:block; padding:20px; text-align:left">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	<h2>Impresszum</h2>
          	<p>A szoftver teljes egészében önkéntes munkával, grátisz lett kifejlesztve.
          	A rendszer üzemeltetése is ilyen formában van megoldva.
          	</p>
          	<p>
          	A szoftver az 
          	<a href="https://www.facebook.com/groups/edemomakers/">
          		informatikusok az e-demokráciáért
          	</a> facebbok csoport támogatásával Fogler Tibor fejlesztette ki és üzemelteti.
          	</p>
          	<p>e-mail: tibor.fogler@gmail.com</p>
          	<p>web: <a href="https://github.com/utopszkij">github.com/utopszkij</a><br />
          	<a href="https://www.facebook.com/utopszkij.almodozo">facebook.com/utopszkij.almodozo</a>
          	</p>
          	<p>Licensz: GNU/GPL</p>
          	<p>
          	A szoftvert mindenki saját felelőségére használhatja, a szoftver használata során az esetleges szoftver hibák által
          	okozott esetleges károkért a fejlesztő semminemű felelőséget nem vállal, még akkor sem ha a hibáról tudomása lehetett.
          	</p>
          	<p>
          	A szoftver üzemeltetése teljes egészében adományokból van finanszírozva. Ha adományok nem érkeznek, akkor
          	sajnos anyagi lehetőségek hiányában a rendszer üzemeltetése leáll. :(
          	</p>
          	<p style="text-align:center">
          		<a href="index.php/opt/adomany/show" target="_self">Adományozás</a>
          	</p>
	      </div><!-- #scope -->
		  <?php $this->echoFooter(); ?>
		  <?php $this->loadJavaScriptAngular('impresszum',$p); ?>
        </body>
        </html>
        <?php 		
	}
}
?>

