<?php
include_once './views/common.php';
class AdomanyView  extends CommonView  {
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
            <h2>Adományozás</h2>
          	<h3>Tájékoztatás</h3>
          	<p>A szoftver teljes egészében önkéntes munkával, grátisz lett kifejlesztve.
          	A rendszergazdai munka is ilyen formában van megoldva. Viszont a működéshez szükséges 
          	VPS szerver pénzbe kerül. Jelenleg a Forpsi-cloud small szervert,ingyenes domaint 
          	és ingyenes https tanusítványt használunk.
          	Az adományokat kizárólag a szerver bérlés finanszirozására használjuk fel.
          	Ha komolyabb érdeklődés lesz a rendszer használatára akkor domain név és minősített https
          	tanusitvány beszerzése és fentartása is szükséges lehet.</p>
          	<p>Minden adományt - akár 1000 Ft -ot is - hálásan köszönünk. Az adományok beérkezéséről és
          	felhasználásáról lentebb részletes elszámolást teszünk közzé.</p>
          	<p>Ha azt kivánja, hogy neve vagy szervezete szerepeljen az elszámolásban akkor azt az utalás
          	közleményében jelezze! Ha a közleményben erről nem rendelkezik az adományt névtelenül
          	szerepeltetjük az elszámolásban.</p>
          	
          	<p>Bankszámla: 11600006-00000000-23190212</p>
          	<p>IBAN: HU75 1160 0006 0000 0000 2319 0212</p>
          	
          	<p>Ethereum tárca: 0xb7233a1474eb3f0359b01A83e57C636DE78C09Da</p>
          	
          	
          	<iframe style="width:600px; height:600px" title="elszamolas"
          	 src="https://docs.google.com/spreadsheets/d/e/2PACX-1vRjHIFVnLDw03ykdEEr72iIot5ONW_8rqkebk1Yz1vM0y-jr6p50SwNkVKrYFkH58YrVwseJf3qfl3t/pubhtml?gid=0&amp;single=true&amp;widget=true&amp;headers=false"></iframe>
          	
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	<p>&nbsp;</p>
          	</div><!-- #scope -->
		  <?php $this->echoFooter(); ?>
		  <?php $this->loadJavaScriptAngular('adomany',$p); ?>
        </body>
        </html>
        <?php 		
	}
}
?>

