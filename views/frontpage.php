<?php
/**
 * Kezdőlap megjelenítés viewer
 */
include_once './views/common.php';

/** viewer osztály */
class FrontpageView  extends CommonView  {
	/**
	* echo html page
	* @param object $p
	* @return void
	*/
	public function display(Params $p) {
	    $this->echoHtmlHead($p);
        ?>	
        <body ng-app="app">
         <div ng-controller="ctrl" id="scope" style="display:none">
	         <?php $this->echoNavbar($p); ?>
			<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
			  <div class="carousel-inner">
			    <div class="carousel-item active">
			      <img class="d-block w-100" src="./templates/default/cover_1.jpg" alt="First slide">
			      <div class="buttons">
			      	<a href="<?php echo MYDOMAIN; ?>/opt/users/login" target="_self">
			      		<em class="fa fa-sign-in"></em>&nbsp;{{txt('LOGIN')}}</a>&nbsp;
			      	<a href="<?php echo MYDOMAIN; ?>/opt/readme/show" target="_self">
			      		<em class="fa fa-info-circle"></em>&nbsp;{{txt('DESC')}}</a>
			      </div>
			       <div class="carousel-caption d-none d-md-block">
				   	<h5><?php echo txt('APPTITLE'); ?></h5>
			   	 	<p><?php echo txt('APPINFO'); ?></p>
				   </div>
			    </div>
			    <div class="carousel-item">
			      <img class="d-block w-100" src="./templates/default/cover_2.jpg" alt="Second slide">
			        <div class="buttons">
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/users/login" target="_self">
			      	  	<em class="fa fa-sign-in"></em>&nbsp;{{txt('LOGIN')}}</a>&nbsp;
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/readme/show" target="_self">
			      	  	<em class="fa fa-info-circle"></em>&nbsp;{{txt('DESC')}}</a>
			        </div>
			       <div class="carousel-caption d-none d-md-block">
				   	<h5><?php echo txt('APPTITLE'); ?></h5>
			   	 	<p><?php echo txt('APPINFO'); ?></p>
				   </div>
			    </div>
			    <div class="carousel-item">
			      <img class="d-block w-100" src="./templates/default/cover_3.jpg" alt="Third slide">
			        <div class="buttons">
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/users/login" target="_self">
			      	  	<em class="fa fa-sign-in"></em>&nbsp;{{txt('LOGIN')}}</a>&nbsp;
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/readme/show" target="_self">
			      	  	<em class="fa fa-info-circle"></em>&nbsp;{{txt('DESC')}}</a>
			        </div>
			       <div class="carousel-caption d-none d-md-block">
				   	<h5><?php echo txt('APPTITLE'); ?></h5>
			   	 	<p><?php echo txt('APPINFO'); ?></p>
				   </div>
			    </div>
			    
			    <div class="carousel-item">
			      <img class="d-block w-100" src="./templates/default/cover_4.jpg" alt="Foorth slide">
			        <div class="buttons">
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/users/login" target="_self">
			      	  	<em class="fa fa-sign-in"></em>&nbsp;{{txt('LOGIN')}}</a>&nbsp;
			      	  <a href="<?php echo MYDOMAIN; ?>/opt/readme/show" target="_self">
			      	  	<em class="fa fa-info-circle"></em>&nbsp;{{txt('DESC')}}</a>
			        </div>
			       <div class="carousel-caption d-none d-md-block">
				   	<h5><?php echo txt('APPTITLE'); ?></h5>
			   	 	<p><?php echo txt('APPINFO'); ?></p>
				   </div>
			    </div>
			    
			  </div>
			  
			  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
			    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
			    <span class="sr-only">{{txt('PRIOR')}}Elöző</span>
			  </a>
			  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
			    <span class="carousel-control-next-icon" aria-hidden="true"></span>
			    <span class="sr-only">{{txt('NEXT')}}</span>
			  </a>
			</div><!-- carousel -->   
			<div class="info">
  				<p>
  				Ez egy minden párttól, szervezettől független civil kezdeményezés. Teljes egészében magán emberek
  				adományaiból működik. A rendszert üzemeltető szerver jelenleg 2019.szeptember 31. -ig van kiifizetve.
  				Amennyiben módja van rá, kérjük támogassa a rendszer működését.
  				</p>
  				<p>
  				  <a href="<?php echo MYDOMAIN; ?>/opt/adomany/show" 
  				     style="background-color:blue; color:white; padding:10px; border-radius:5px;">
  					Támogatás
  				  </a>
  				</p>
  			</div>
  			
			<div id="suggest">
				<div style="text-align:center">
					<p> </p>
					<p class="lead"><strong>Ajánlott oldalak</strong></p>
					<div class="suggest-items" style="display:inline-block; width:auto">
										<div class="suggest-item" style="background-image:url('images/recent/item1.png')">
											<div class="popuptitle">
													<h3>Szellemi termelési mód</h3>
													<p>Kapitány Ágnes és Gábor könyve egy lehetséges új termelési mód körvonalait vázolja.</p>
													<a href="https://hu.wikipedia.org/wiki/Szellemi_termel%C3%A9si_m%C3%B3d"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item2.png')">
											<div class="popuptitle">
													<h3>Internet filozófia</h3>
													<p>A 'blogoló' a tulajdonképpeni, a teljes jogú, a teljes fegyverzetében előttünk álló hálópolgár, a kiberkultúra eminens létrehozója.</p>
													<a href="http://internetfilozofia.blog.hu/"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item3.png')">
											<div class="popuptitle">
													<h3>Információs társadalom</h3>
													<p>Wikipedia szó cikk.</p>
													<a href="https://hu.wikipedia.org/wiki/Inform%C3%A1ci%C3%B3s_t%C3%A1rsadalom_(fogalom)"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item4.png')">
											<div class="popuptitle">
													<h3>Katedrális és bazár</h3>
													<p>A "szabadszoftver" világ alapműve.</p>
													<a href="http://magyar-irodalom.elte.hu/robert/szovegek/bazar/"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item5.png')">
											<div class="popuptitle">
													<h3>Szelid pénz</h3>
													<p>Egy alternatív pénzrendszer....</p>
													<a href="http://edok.lib.uni-corvinus.hu/284/1/Szalay93.pdf"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item6.png')">
											<div class="popuptitle">
													<h3>Likvid demokrácia</h3>
													<p>A napjainkra már teljesen kiüresedett, funkcióját vesztett képviseleti demokrácia egy lehetséges utóda.</p>
													<a href="http://hu.alternativgazdasag.wikia.com/wiki/Likvid_demokr%C3%A1cia"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item7.png')">
											<div class="popuptitle">
													<h3>Vénusz projekt</h3>
													<p>Egy átfogó jövő kép...</p>
													<a href="https://www.youtube.com/watch?v=Uh9VxaO12zY&list=PL255C39DA73A5F10B&index=149"><em class="fa fa-eye"></em>Röviditett magyar szinkronos video</a><br>
													<a href="https://www.youtube.com/watch?v=JcbMW5Y5HxY"><em class="fa fa-eye"></em>Teljes magyar feliratos video</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/item8.png')">
											<div class="popuptitle">
													<h3>Feltétel nélküli alapjövedelem</h3>
													<p>Ezt akár holnap megcsinálhatnánk....</p>
													<a href="http://alapjovedelem.hu/index.php/gyik"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/finger-3139200_640.jpg')">
											<div class="popuptitle">
													<h3>Alternatív gazdaság</h3>
													<p>Nem a (kapitalista) piacgazdaság az egyetlen elképzelhető technológia a társadalmi munkamegosztás megszervezésére.</p>
													<a href="http://hu.alternativgazdasag.wikia.com/wiki/Alternat%C3%ADv_Gazdas%C3%A1g_lexikon"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
										<div class="suggest-item" style="background-image:url('images/recent/human-1157116_640.jpg')">
											<div class="popuptitle">
													<h3>>Megosztás alapú gazdaság</h3>
													<h4>Ez is egy koncepció...</h4>
													<a href="https://medium.com/envienta-magyarorsz%C3%A1g/envienta-%C3%BAtban-egy-%C3%BAj-t%C3%A1rsadalom-fel%C3%A9-43e6b72c3a2c"><em class="fa fa-eye"></em>Megnézem</a>
											</div>
										</div>
					</div>
				</div>
				<div style="clear:both"></div>  			
			   
        	<?php $this->echoHtmlPopup(); ?>
          </div><!-- #scope -->
		  <?php $this->echoFooter(); ?>
          <?php $this->loadJavaScriptAngular('frontpage',$p); ?>
        </body>
        </html>
        <?php 		
	}
}
?>