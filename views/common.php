<?php
class CommonView extends View {
   
    /**
     * echo succes message after add new app
     * @param string $backLink
     * @param string $backStr
     * @param Params $p {user, userAdmin, avatarUrl,....}
     * @return void;}
     */
    public function successMsg(array $msgs,  string $backLink='', string $backStr='', Params $p) {
        global $REQUEST;
        $this->echoHtmlHead($p);
        ?>
        <body ng-app="app">
	    <div ng-controller="ctrl" id="scope" style="display:none" class="successMsg">
            <?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
    	    <h2 class="alert alert-success">
    			<?php 
    			foreach ($msgs as $msg) {
    			    echo txt($msg).'<br />';
    			}
    			?>
    	    </h2>
    	    <?php if ($backLink != '') : ?>
    	    <p style="text-align:center">
    	    	<a class="btn btn-primary" href="<?php echo $backLink; ?>" target="_self">
    	    		<?php echo txt($backStr); ?>
    	    	</a>
    	    </p>	
    	    <?php endif; ?>
	    </div>
        </body>
        <?php $this->loadJavaScriptAngular('frontpage',$p); ?>
        </html>
        <?php 
	}
    
	/**
	 * echo fatal error in app save
	 * @param array of string messages
	 * @param string backLink
	 * @param string backLinkText
	 * @param object $p {user, userAdmin, avatarUrl,....}
	 * @return void
	 */
	public function errorMsg(array $msgs, string $backLink='', string $backStr='', $p) {
	    global $REQUEST;
	    $this->echohtmlHead($p);
	    ?>
        <body ng-app="app">
	    <div ng-controller="ctrl" id="scope" style="display:none" class="errorMsg">
        <?php $this->echoHtmlPopup(); ?>
        <?php  $this->echoNavbar($p);  ?>
	    <h2 class="alert alert-danger">
			<?php 
			foreach ($msgs as $msg) {
			    echo txt($msg).'<br />';
			}
			?>
	    </h2>
	    </div>
	    <?php if ($backLink != '') : ?>
	    <p style="text-align:center">
	    	<a class="btn btn-primary" href="<?php echo $backLink; ?>" target="_self">
	    		<?php echo txt($backStr); ?>
	    	</a>
	    </p>
	    <?php endif; ?>
        <?php $this->loadJavaScriptAngular('frontpage', $p); ?>
        </body>
        </html>
        <?php 
	}
    
	/**
	* echo navbar html code
	* @param object $p - {$user->nick, $user->avatar, ...}
	* @return void
	*/
	public function echoNavbar($p) {
         ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active homeMenuItem">
                <a class="nav-link" href="<?php echo MYDOMAIN; ?>" target="_self">
                	<img src="favicon.png" alt="favicon"/>
                	netpolgar  
                </a>
              </li>
              <li class="nav-item active groupMenuItem">
                <a class="nav-link" 
                  	href="{{LNG.MYDOMAIN}}/opt/groups/list/parentid/0/userid/0" target="_self">
                  	<em class="fa fa-users">&nbsp;</em>{{txt('GROUPS')}}</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" 
                	href="{{LNG.MYDOMAIN}}/opt/setup/form" id="navbarDropdown"  target="_self" 
                	role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em class="fa fa-cog"></em>{{txt('SETUP')}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/categories/list" target="_self">{{txt('CATEGORIES')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/usergroups/list" target="_self">{{txt('USERGROUPS')}}</a>
                  <a class="dropdown-item" ng-if="(userAdmin)" 
                  	href="<?php echo MYDOMAIN ;?>/opt/users/list" target="_self" >
                  	{{txt('USERS')}}
                  </a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" 
                   href="{{LNG.MYDOMAIN}}/opt/projects/list" id="navbarDropdown"  target="_self"
                   role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em class="fa fa-wrench"></em>{{txt('PROJECTS')}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/projects/active" target="_self">{{txt('ACTIVE')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/projects/closed" target="_self">{{txt('CLOSED')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/projects/draft" target="_self">{{txt('DRAFT')}}</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" 
                  href="{{LNG.MYDOMAIN}}/opt/market/main" id="navbarDropdown" target="_self"
                  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em class="fa fa-shopping-cart"></em>{{txt('MARKET')}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/market/offer" target="_self">{{txt('OFFER')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/market/demand" target="_self">{{txt('DEMAND')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/market/transactions" target="_self">{{txt('TRANSACTIONS')}}</a>
                </div>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="{{LNG.MYDOMAIN}}/opt/files/list" target="_self">
                	<em class="fa fa-file"></em>{{txt('DOCS')}}  
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" 
                  href="{{LNG.MYDOMAIN}}/opt/disputes/list" id="navbarDropdown"  target="_self"
                  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em class="fa fa-comments"></em>{{txt('DISPUTES')}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/chats/list" target="_self">{{txt('CHATS')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/polls/list" target="_self">{{txt('POLLS')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/decisions/list" target="_self">{{txt('DECISIONS')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/messages/list" target="_self">{{txt('MESSAGES')}}</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="{{LNG.MYDOMAIN}}/opt/events/list" target="_self" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em class="fa fa-calendar"></em>{{txt('EVENTS')}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/events/follows" target="_self">{{txt('FOLLOWS')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/events/olds" target="_self">{{txt('OLDS')}}</a>
                  <a class="dropdown-item" href="{{LNG.MYDOMAIN}}/opt/events/all" target="_self">{{txt('ALL')}}</a>
                </div>
              </li>
              <li class="nav-item dropdown userMenuItem" ng-if="loggedUser.nick != 'guest'">
                <a class="nav-link dropdown-toggle"
                  href="{{LNG.MYDOMAIN}}/opt/users/profile" target="_self" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <em ng-if="user.avatar == ''" class="fa fa-user"></em>
                  <strong>{{loggedUser.nick}}</strong>
                  <img ng-if="avatarUrl != ''" src="{{avatarUrl}}" alt="avatar" />
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="<?php echo MYDOMAIN?>/opt/users/profile" target="_self"
                  	 target="_self">{{txt('MYDATA')}}</a>
                  <a class="dropdown-item" 
                     target="_self" href="<?php echo MYDOMAIN?>/opt/users/removeaccount">{{txt('REMOVE_ACCOUNT')}}</a>
                  <a class="dropdown-item" href="<?php echo MYDOMAIN; ?>/opt/users/logout" target="_self">{{txt('LOGOUT')}}</a>
                </div>
              </li>
              <li class="nav-item active userMenuItem" ng-if="loggedUser.nick == 'guest'">
                <a class="nav-link" href="<?php echo MYDOMAIN.'/opt/users/login'?>" 
                    target="_self">
                	<em class="fa fa-sign-in-alt"></em>{{txt('LOGIN')}}  
                </a>
              </li>
            </ul>
          </div>
        </nav>
		<?php       
     } // echoNavbar
        
     /**
      * echo footer html code
      */
     function echoFooter() {
         global $REQUEST;
        ?> 
      	<div id="footer">  
      	<p>
			<a href="<?php echo txt('MYDOMAIN'); ?>/opt/impresszum/show" target="_self">
				<em class="fa fa-pencil"></em>&nbsp;<?php echo txt('IMPRESSUM'); ?></a>&nbsp;&nbsp;&nbsp;      
			<a href="<?php echo txt('MYDOMAIN'); ?>/opt/adatkezeles/show" target="_self">
				<em class="fa fa-lock"></em>&nbsp;<?php echo txt('DATAPROCESS'); ?></a>&nbsp;&nbsp;&nbsp;      
			<a href="http://gnu.hu/gpl.html" target="_self">
				<em class="fa fa-copyright"></em>&nbsp;<?php echo txt('LICENCE'); ?>: GNU/GPL</a>&nbsp;&nbsp;&nbsp;      
			<a href="https://github.com/utopszkij/netpolgar" target="_self">
				<em class="fa fa-code"></em>&nbsp;<?php echo txt('SOURCE'); ?></a>&nbsp;&nbsp;&nbsp;   
			<a href="<?php echo MYDOMAIN; ?>/opt/issu/form" target="_self">
				<em class="fa fa-bug"></em>&nbsp;<?php echo txt('BUGMSG'); ?></a>&nbsp;&nbsp;&nbsp;   
      	</p>   
		<p><?php echo txt('SWRESOURCE'); ?>:			
				<a href="https://www.php.net/manual/en/index.php" target="_self">php</a>&nbsp;
				<a href="https://fontawesome.com/icons?d=gallery" target="_self">fontAwesome</a>&nbsp;
				<a href="https://www.w3schools.com/css/" target="_self">css</a>&nbsp;
				<a href="https://getbootstrap.com/" target="_self">bootstrap</a>&nbsp;
				<a href="https://jquery.com/" target="_self">Jquery</a>&nbsp;
				<a href="https://angularjs.org/" target="_self">AngularJs</a>&nbsp;
				<a href="https://www.fpdf.org" target="_self">fpdf</a>&nbsp;
				<a href="https://github.com/smalot/pdfparser" target="_self">smalot</a>&nbsp;
				<a href="https://github.com/tan-tan-kanarek/github-php-client">
					tan-tan-kanarek_github_kliens
				</a>&nbsp;
				<a href="https://github.com/jonmiles/bootstrap-treeview" target="_self">
					bootsrap-treeview
				</a>
				<a href="https://pixabay.com/hu/" target="self">pixabay</a>
		</p>
		<p>Teszteléshez: phpunit&nbsp;mocha&nbsp;sonar-cloud&nbsp;</p>
		</div>
        <?php 		
        $cookieEnabled = $REQUEST->sessionGet('cookieEnabled',false);
        if (!$cookieEnabled) {
            ?>
            <div id="cookieEnable" 
            	 style="position:fixed; z-index:99; width:100%; height:auto; left:0px; bottom:0px; background-color:#c0f0c0; opacity: 0.8; padding:2px; margin:0px;">
             	<p style="margin:0px; padding:0px; text-align:right">A rendszer használatához engedélyeznie kell egy munkamenet cookie használatát.
                 	&nbsp;Lásd: <a href="index.php/opt/adatkezeles/show" target="_new">Adatkezelési leírás</a>
    				<a class="btn btn-primary" target="_self"
    				  href="index.php?cookieenabled=1">Engedélyezem
    				</a>
				</p>
            </div>
             <?php
             $REQUEST->sessionSet('cookieEnabled',$cookieEnabled);
        } else {
             ?>
             <div id="cookieDisable" style="text-align:center; padding:5px;">
             	<p style="margin:0px; padding:0px; text-align:right">A rendszer egy munkamenet cookie-t használ.
                 	&nbsp;Lásd: <a href="index.php/opt/adatkezeles/show" target="_new">Adatkezelési leírás</a>
    				<a class="btn btn-primary" target="_self"
    				  href="index.php?cookieenabled=0">
    				  Munkamenet cookie tiltása
    				</a>
				</p>
             </div>
             <?php 
             $REQUEST->sessionSet('cookieEnabled',$cookieEnabled);
         }
     }
     
     /**
      * echo paginator
      * @param int $total
      * @param int $offset
      * @param int $limit
      */
     public function echoPaginator(int $total, int $offset, int $limit) {
         $offsetPrev = $offset - $limit;
         $offsetLast = 0;
         if ($offsetPrev < 0) {
             $offsetPrev = 0;
         }
         echo '<ul class="pagination">';
         echo '<li class="page-item disabled"><a class="page-link disabled">'.txt('TOTAL').': '.$total.' '.txt('PAGES').':</a></li>';
         if ($offset > 0) {
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick(0)">
                <em class="fa fa-backward" title="'.txt('FIRST').'"></em>
              </a></li>';
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetPrev.')">
                <em class="fa fa-caret-left" title="'.txt('PRIOR').'"></em></a></li>';
         }
         $p = 1;
         for ($o = 0; $o < $total; $o = $o + $limit) {
             if ($o == $offset) {
                 echo '<li class="page-item active"><a href=""  class="page-link disabled" onclick="false">'.$p.'</a></li>';
             } else {
                 echo '<li class="page-item"><a href="#"  class="page-link" onclick="paginatorClick('.$o.')">'.$p.'</a></li>';
             }
             $offsetLast = $o;
             $p = $p + 1;
         }
         $offsetNext = $offset + $limit;
         if ($offsetNext >= $offsetLast) {
             $offsetNext = $offsetLast;
         }
         if ($offset < $offsetLast) {
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetNext.')">
                <em class="fa fa-caret-right" title="'.txt('NEXT').'"></em></a></li>';
             echo '<li class="page-item"><a href="#" class="page-link" onclick="paginatorClick('.$offsetLast.')">
                <em class="fa fa-forward" title="'.txt('LAST').'"></em></a></li>';
         }
         echo '</ul>';
         echo '</div>';
     }
     
}
?>