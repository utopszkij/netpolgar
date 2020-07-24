<?php
/**
 * user kezelés viewer
 */
include_once './views/common.php';

/** user kezelés viewer osztály */
class UsersView  extends CommonView  {

	/**
	 * profil form megjelenitése
	 * @param object $p - loggedUser, userData, backUrl
	 */
	public function profileForm(Params $p) {
	    foreach ($p->loggedUser as $fn => $fv) {
	        $p->$fn = $fv;
	    }
	    $this->setTemplates($p,['userssubmenu','usersform']);
	    $this->echoHtmlPage('usersprofile', $p, 'users');
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

