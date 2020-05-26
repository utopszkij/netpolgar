<?php
/**
 * user kezelés viewer
 */
include_once './views/common.php';

/** user kezelés viewer osztály */
class UsersView  extends CommonView  {

    /**
     * user submenü html kod kirajzolása
     * @param object $p form paraméterek
     */
    public function echoUserSubmenu($p) {
        ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light subMenu"
            id="userSubmenu" ng-if="id > 0">
            <button class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#userSubmenuContent"
                aria-controls="userSubmenuContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <em class="fa fa-ellipsis-v"></em>
            </button>
                     
            <div class="collapse navbar-collapse" id="userSubmenuContent">
                 <ul>
                     <li>
                        <a class="nav-link" target="_self"
                            href="<?php echo MYDOMAIN.'/opt/users/rights/userid/'.$p->id; ?>">
                            <span class="fa fa-key"></span>{{txt('USER_RIGHTS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                             href="<?php echo MYDOMAIN.'/opt/groups/list/userid/'.$p->id; ?>">
                             <span class="fa fa-sitemap"></span>{{txt('GROUPS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                              href="<?php echo MYDOMAIN.'/opt/projects/list/userid/'.$p->id; ?>">
                              <span class="fa fa-cogs"></span>{{txt('PROJECTS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                               href="<?php echo MYDOMAIN.'/opt/market/list/userid/'.$p->id; ?>">
                               <span class="fa fa-shopping-basket"></span>{{txt('MARKET')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                                href="<?php echo MYDOMAIN.'/opt/market/transactions/userid/'.$p->id; ?>">
                                <span class="fa fa-truck"></span>{{txt('TRANSACTIONS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                                 href="<?php echo MYDOMAIN.'/opt/files/list/userid/'.$p->id; ?>">
                                 <span class="fa fa-file"></span>{{txt('DOCS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                                 href="<?php echo MYDOMAIN.'/opt/chats/list/userid/'.$p->id; ?>">
                                 <span class="fa fa-comments"></span>{{txt('CHATS')}}
                         </a>
                      </li>
                      <li>
                         <a class="nav-link" target="_self"
                              href="<?php echo MYDOMAIN.'/opt/voks/list/userid/'.$p->id; ?>">
                              <span class="fa fa-balance-scale"></span>{{txt('POLLS')}}
                         </a>
                      </li>
                      <li>
                         <a class="nav-link" target="_self"
                                  href="<?php echo MYDOMAIN.'/opt/events/list/userid/'.$p->id; ?>">
                                  <span class="fa fa-calendar"></span>{{txt('EVENTS')}}
                         </a>
                      </li>
                      <li>
                         <a class="nav-link" target="_self"
                                   href="<?php echo MYDOMAIN.'/opt/messages/list/userid/'.$p->id; ?>">
                                   <span class="fa fa-comment"></span>{{txt('MESSAGES')}}
                         </a>
                      </li>
                 </ul>
            </div>
      </nav>
      <?php                                                            
    }
    
    /**
     * user form kirajzolása
     * ez a rutin az új user regisztrálásánál és a profil képernyőnél is van használva
     * @param object $p form paraméterek
     */
    public function echoUserForm(Params $p) {
        ?>
              <form class="form-row" id="formRegform"
              	action="<?php echo MYDOMAIN; ?>/opt/users/add"
              	target="_self" method="post">
              	<input type="hidden" name="{{csrToken}}" value="1" />
              	<input type="hidden" name="id" id="id" value="{{id}}" />
        		<input type="hidden" name="enabled" value="{{enabled}}" />
        		<input type="hidden" name="errorcount" value="{{errorcount}}" />
        		<input type="hidden" name="block_time" value="{{block_time}}" />
        		<input type="hidden" name="reg_mode" id="reg_mode" value="{{reg_mode}}" />
        		<input type="hidden" name="backUrl" id="backUrl" value="{{backUrl}}" />
                <p id="pAvatar" ng-if="avatarUrl > 'a'">
                <img id="imgAvatar" src="{{avatarUrl}}" style="display:none" alt="avatar" />
                </p>
                <p>
              		<label>{{txt('NICK')}} *:</label>
              		<input id="nick" name="nick" type="text" value="{{nick}}"  size="32" />
              		<input id="origNick" name="origNick" type="hidden" value="{{nick}}"  size="32" />
              	</p>
              	<p>
              		<label>{{txt('PSW')}} *:</label>
              		<input id="psw" name="psw" type="password" value=""  size="32" />
              	</p>
              	<p>
              		<label>{{txt('PSW_REPEAT')}} *:</label>
              		<input id="psw2" name="psw2" type="password" value=""  size="32" />
              	</p>
              	<p>
              		<label>{{txt('VALID_NAME')}} *:</label>
              		<input id="name" name="name" type="text" value="{{name}}"  size="50" />
              	</p>
              	<p>
              		<label>{{txt('EMAIL')}} *:</label>
              		<input id="email" name="email" type="text" value="{{email}}"  size="50" />
              	</p>
              	<p>
              		<label>{{txt('AVATAR')}}:</label>
              		<input id="avatar" name="avatar" type="text" value="{{avatar}}"  size="50" />
              		<button type="button" id="btnAvatarShow">
              			<em class="fa fa-eye"></em>
              		</button>
              	</p>
              	<p ng-if="id > 0">{{txt('USRPROFILE_HELP')}}</p>
			    <div class="clear"><br /></div>
              	<p class="formButtons">
              		<button type="button" id="btnOk" class="btn btn-primary">
              			<em class="fa fa-check"></em>{{txt('OK')}}</button>&nbsp;
              		<button type="button" id="btnCancel" class="btn btn-secondary">
              			<em class="fa fa-arrow-left"></em>{{txt('CANCEL')}}</button>&nbsp;
              		&nbsp;&nbsp;&nbsp;	
              		<button type="button" id="btnRemove" class="btn btn-danger" ng-if="id > 0">
              			<em class="fa fa-times-circle"></em>{{txt('REMOVE_ACCOUNT')}}</button>&nbsp;
              	</p>
              </form>
        <?php
    }
    
    /**
     * regisztrációs form kirajzolása
     * - reg.mód select
     * - rejtetten mindkét fajta regist form
     * - Js kód teszi a megfelelőt láthatóvá
     * @param RequestObject $request
     *   title, user rekord mezői, auditor, opcionális: ca'; // helszó hash
     */
	public function registForm(Params $p) {
	    $ukloginUrl = 'https://uklogin.tk/openid/authorize';
	    $this->echoHtmlHead($p);
        ?>	
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	
          	<div id="registForm">
            <h2>{{txt(title)}}</h2>

			<div class="alert alert-danger" ng-if="msgs.length > 0">
				<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
			</div>
            <div id="webRegform">
                <p class="altLogin">	
                  	<a class="btn btn-outline-secondary" target="ukloginFrm"
                  		onclick="$('#divUklogin').show()"
                  		href="<?php echo $ukloginUrl; ?>">
    			   	    <img src="images/uklogin.png" style="height:40px" />
                  		Belépés ügyfélkapuval
                  	</a>
                </p>	
                <p class="altLogin">	
    			   	<button type="button" class="btn btn-outline-secondary"  
    			   	     onclick="location='<?php echo config('MYDOMAIN'); ?>/opt/fblogin/authorize';">
    			   	     <img src="images/facebook.png" style="height:40px" />
    			   	 	 Belépés facebook -al
    			    </button>
    		  	</p>          
                <p class="altLogin">	
    			   	<button type="button" class="btn btn-outline-secondary"  
    			   	     onclick="location='<?php echo config('MYDOMAIN'); ?>/opt/googlelogin/authorize';">
    			   	     <img src="images/google.png" style="height:22px" />
    			   	 	 Belépés google -el
    			    </button>
    		  	</p>          
              <?php $this->echoUserForm($p); ?>	
            </div>
            <div id="divUklogin">
              		<p style="text-align:right">
              			<button type="button" class="btn" onclick="$('#divUklogin').hide()">
              				<em class="fa fa-times" title="{{LNG.CLOSE}}"></em>
              			<button>
              		</p>
                	<iframe name="ukloginFrm" style="width:890px; height:750px; border-style:none" title="uklogin"></iframe>
            </div>
          </div><!-- #registForm -->  
		  <div class="clear"></div>
		  <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
        </body>
        </html>
        <?php 		
	}

	/**
	 * Bejelentkezés képernyő
	 * @param Params $p
	 */
	public function loginForm(Params $p) {
	    $ukloginUrl = 'https://uklogin.tk/openid/authorize';
	    $this->echoHtmlHead($p);
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	
          	<div id="loginForm">
              <h2>{{txt('LOGIN')}}</h2>

			  <div class="alert alert-danger" ng-if="msgs.length > 0">
					<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
			  </div>

              <form id="formLogin" method="post" target="_self"
                    action="<?php echo MYDOMAIN?>/opt/users/dologin">
              <input type="hidden" name="{{csrToken}}" value="1" />
              <p>
              	<label>{{txt('NICK')}}*</label> 
              	<input type="text" name="nick" id="nick" />
              </p>
              <p>
              	<label>{{txt('PSW')}}*</label>
              	<input type="password" name="psw" />
              </p>
              <p>
              	<button type="submit" class="btn btn-primary">
              		<em class="fa fa-check"></em>&nbsp;
              		{{txt('OK')}}
              	</button>
              </p>
              <p class="loginLinkek">
              	<ul>
              		<li><a href="<?php echo MYDOMAIN;?>/opt/users/regist" target="_self">
              				{{txt('REGIST')}}</a></li>
              		<li><a href="#" target="_self" id="linkForgetPsw">{{txt('FORGET_MY_PASSWORD')}}</a></li>
              		<li><a href="<?php echo MYDOMAIN?>/opt/users/forgetnick" target="_self"
              		       id="forgetNick">{{txt('FORGET_MY_NICK')}}</a></li>
              		<li><a href="#" target="_self" id="linkGetActivateEmail">{{txt('GET_ACTIVATE_EMAIL')}}</a></li>
              	</ul>
              </p>
              <p class="altLogin">	
              	<a class="btn btn-outline-secondary" target="ukloginFrm"
              		onclick="$('#divUklogin').show()"
              		href="<?php echo $ukloginUrl; ?>">
			   	    <img src="images/uklogin.png" style="height:40px" />
              		Belépés ügyfélkapuval
              	</a>
              </p>	
              <p class="altLogin">	
			   	<button type="button" class="btn btn-outline-secondary"  
			   	     onclick="location='<?php echo config('MYDOMAIN'); ?>/opt/fblogin/authorize';">
			   	     <img src="images/facebook.png" style="height:40px" />
			   	 	 Belépés facebook -al
			    </button>
		  	  </p>          
              <p class="altLogin">	
			   	<button type="button" class="btn btn-outline-secondary"  
			   	     onclick="location='<?php echo config('MYDOMAIN'); ?>/opt/googlelogin/authorize';">
			   	     <img src="images/google.png" style="height:22px" />
			   	 	 Belépés google -el
			    </button>
		  	  </p>          
              </form>



          </div><!-- .loginForm -->
          
          <div id="divUklogin">
          		<p style="text-align:right">
          			<button type="button" class="btn" onclick="$('#divUklogin').hide()">
          				<em class="fa fa-times" title="{{LNG.CLOSE}}"></em>
          			<button>
          		</p>
            	<iframe name="ukloginFrm" style="width:890px; height:750px; border-style:none" title="uklogin"></iframe>
          </div>
          
            
		  <div class="clear"></div>
		  <?php $this->echoFooter(); ?>
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
	      </div><!-- #scope -->
        </body>
        </html>
        <?php 		
	}
	
	/**
	 * Elfejeltettem a nick nevem -email bekérő képernyő
	 * @param object $p
	 */
	public function forgetNick(Params $p) {
	    $this->echoHtmlHead($p);
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	
          	<div id="forgetNick">
              <h2>{{txt('FORGET_MY_NICK')}}</h2>

              <form id="formForgetNick" method="post" target="_self"
                    action="<?php echo MYDOMAIN?>/opt/users/forgetpsw">
              <input type="hidden" name="{{csrToken}}" value="1" />
              <p>
              	<label>{{txt('EMAIL')}}*</label> 
              	<input type="text" name="email" id="email" />
              	<button type="submit" class="btn btn-primary">{{txt('OK')}}</button>
              </p>
              </form>
          </div><!-- forgetNick -->
		  <div class="clear"></div>
		  <?php $this->echoFooter(); ?>
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
	      </div><!-- #scope -->
        </body>
        </html>
	    <?php
	}
	
	/**
	 * profil form megjelenitése
	 * @param object $p - loggedUser, userData, backUrl
	 */
	public function profileForm(Params $p) {
	    $this->echoHtmlHead($p);
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
        	<div id="divProfileForm">  
				<?php
				foreach ($p->userData as $fn => $fv) {
	               $p->$fn = $fv;
	            }
	            $p->avatarUrl = $p->userDataAvatarUrl;
                ?>    
        		<h2>{{txt('PROFILE')}}</h2>  
   			
				<div class="alert alert-danger" ng-if="msgs.length > 0">
					<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
				</div>
   			
   				<?php $this->echoUserSubmenu($p); ?>
   				<div id="divProfileForm2">
			    	<?php $this->echoUserForm($p); ?>
			    </div>
			</div>    
		    <div class="clear"></div>
		    <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
        </body>
        </html>
	    <?php
	}
	
	/**
	 * user törlés megerősítő kérdés
	 * @param object $p
	 */
	public function removeaccount(Params $p) {
	    $this->echoHtmlHead($p);
	    foreach ($p->user as $fn => $fv) {
	        $p->$fn = $fv;
	    }
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
        	<div id="removeaccount">  
        		<form action="<?php echo MYDOMAIN; ?>/opt/users/doremoveaccount" 
        		    method="post" target="_self">
        		    <input type="hidden" name="{{csrToken}}" value="1" />
        		    <input type="hidden" name="backUrl" value="{{backUrl}}" />
        		    <input type="hidden" name="userId" value="{{userId}}" />
            		<h2>{{txt('REMOVE_ACCOUNT')}}</h2>
            		<p><strong>{{userData.nick}} / {{userData.name}}</strong></p>
            		<p>{{txt('SURE_REMOVE_ACCOUNT')}}</p>
            		<p>
            			<button type="submit" class="btn btn-danger">{{txt('YES')}}</button>&nbsp;
            			<a href="<?php echo $p->backUrl; ?>" target="_self" class="btn btn-secondary">{{txt('NO')}}</a>
            		</p>  
        		</form>
			</div>    
		    <div class="clear"></div>
		    <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
        </body>
        </html>
	    <?php
	}
	
	
	/**
	 * user böngésző
	 * @param object $p (items, offset, limit, orderField, orderDir, filterStr
	 */
	public function browser(Params $p) {
	    $this->echoHtmlHead($p);
	    foreach ($p->user as $fn => $fv) {
	        $p->$fn = $fv;
	    }
	    $backUrl = MYDOMAIN.'/opt/users/list';
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
        	<div id="usersList">  
        		<form id="formUsersList" action="<?php echo MYDOMAIN; ?>/opt/users/list" 
        		    method="post" target="_self">
        		    <input type="hidden" name="{{csrToken}}" value="1" />
        		    <input type="hidden" name="offset" id="offset" value="{{offset}}" />
        		    <input type="hidden" name="limit" id="limit" value="{{limit}}" />
        		    <input type="hidden" name="orderField" id="orderField" value="{{orderField}}" />
        		    <input type="hidden" name="orderDir" id="orderDir" value="{{orderDir}}" />
            		<h2>{{txt('USERS_LIST')}}</h2>
       			    <div class="search">            
                          <input id="filterStr" name="filterStr"
                           type="search" placeholder="{{txt('SEARCH_TXT')}}"
                           value="{{filterStr}}" style="width:200px" />
                          <button class="btn btn-primary" type="submit">
                          	<em class="fa fa-search"></em>{{txt('SEARCH')}}
                          </button>
                          <button class="btn btn-secondary"
                             type="submit" onclick="$('#filterStr').val(''); true">
                          	x
                          </button>
			        </div>  
            		
            		<table class="table table-striped" summary="usersDetails">
            			<thead class="thead-dark">
            				<tr>
            					<th id="thId">{{txt('ID')}}&nbsp;<em class="fa"></em></th>
            					<th id="thNick">{{txt('NICK')}}&nbsp;<em class="fa"></em></th>
            					<th id="thName">{{txt('VALID_NAME')}}&nbsp;<em class="fa"></em></th>
            				</tr>
            			<thead>
            			<tbody>
            				<tr ng-repeat="item in items" scope="row"  class={{item.trClass}}
            				    onclick="trClick(event)" id="tr_{{item.id}}">
            					<td class="tdId">{{item.id}}</td>
            					<td class="tdNick">{{item.nick}}</td>
            					<td class="tdName">{{item.name}}</td>
            				</tr>
           				</tbody>
            		</table>
            		<?php $this->echoPaginator($p->total, $p->offset, $p->limit); ?>
        		</form>
			</div>    
		    <div class="clear"></div>
		    <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
	      <script type="text/javascript">
	        /**
	        * tr click - user adatlap url hívása
	        * @param mouseEvent event  event.target.parentNode.id = "id_#####
		    */    
			function trClick(event) {
				console.log(event.target.parentNode.id);
				var id = event.target.parentNode.id.substring(3,100);
				window.location='<?php echo MYDOMAIN; ?>/opt/users/profile'+
				'/userid/'+id+
				'/<?php echo $p->csrToken; ?>/1/w/2'+
				'?backUrl=<?php echo urlencode($backUrl); ?>';
			}
			/**
			* paginátor click rutin - offset beállítása, form submit
			* @param int offset
			*/
			function paginatorClick(offset) {
				  $('#offset').val(offset);
				  $('#formUsersList').submit();
				  console.log('paginatorClick '+offset);
				  return false;
			}
	      </script>
		  <?php $this->loadJavaScriptAngular('users',$p); ?>
        </body>
        </html>
	    <?php
	}
	
}
?>

