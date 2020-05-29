<?php
include_once './views/common.php';
class GroupsView  extends CommonView  {

    /**
     * group submenü html kod kirajzolása
     * @param object $p  item form paraméterek, groupUserId, userGroupAdmin
     */
    public function echoGroupSubmenu(Params $p) {
        ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light subMenu"
            id="grouprSubmenu">
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
                            href="<?php echo MYDOMAIN.'/opt/members/list/type/group/objectid/'.$p->groupId; ?>">
                            <span class="fa fa-user"></span>{{txt('MEMBERS')}}
                         </a>
                     </li>
                     <li>
                        <a class="nav-link" target="_self"
                            href="<?php echo MYDOMAIN.'/opt/groups/list/parentid/'.$p->groupId; ?>">
                            <span class="fa fa-sitemap"></span>{{txt('SUB_GROUPS')}}
                         </a>
                     </li>
                     
                     <li>
                         <a class="nav-link" target="_self"
                             href="<?php echo MYDOMAIN.'/opt/projects/list/groupid/'.$p->groupId; ?>">
                             <span class="fa fa-cogs"></span>{{txt('PROJECTS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                              href="<?php echo MYDOMAIN.'/opt/market/list/groupid/'.$p->groupId; ?>">
                              <span class="fa fa-shopping-basket"></span>{{txt('MARKET')}}
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
                              href="<?php echo MYDOMAIN.'/opt/groups/chats/groupid/'.$p->groupId; ?>">
                              <span class="fa fa-comments"></span>{{txt('CHATS')}}
                         </a>
                     </li>
                     <li>
                         <a class="nav-link" target="_self"
                              href="<?php echo MYDOMAIN.'/opt/voks/list/groupid/'.$p->groupId; ?>">
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
     * group form kirajzolása
     * ez a rutin az új group felvitelénél és modosításánál van használva
     * avatar kép feltöltést is lehetővé tesz
     * @param object $p  $p->item form mezők, $->filterUser, $p->formTitle
     */
    public function echoGroupForm(Params $p) {
        ?>
              <form class="form-row" id="formGroupForm"
              	action="<?php echo MYDOMAIN; ?>/opt/groups/save"
              	target="_self" method="post">
              	<input type="hidden" name="{{csrToken}}" value="1" />
              	<input type="hidden" name="groupid" id="id" value="{{item.id}}" />
              	<input type="hidden" name="id" id="id" value="{{item.id}}" />
              	<input type="hidden" name="parent" id="parent" value="{{item.parent}}" />
        		<input type="hidden" name="backUrl" id="backUrl" value="{{backUrl}}" />
                <?php if ($p->item->avatar != '') : ?>
                <img id="imgAvatar" src="{{item.avatar}}" alt="avatar" />
                <?php endif; ?>
                <p>
              		<label>{{txt('NAME')}} *:</label>
                    <div class="memberButtons">
                  		<input id="name" name="name" type="text" value="{{item.name}}"  size="80" />
                  		<br />                 
                  		<var ng-if="loggedUser.id > 0">{{txt('USERSTATE')}}: {{txt(userState)}}</var>
     					<a href="{{LNG.MYDOMAIN}}/opt/members/aspire/type/group/id/{{groupId}}/userid/{{loggedUser.id}}/{{csrToken}}/1" 
        					     class="btn btn-secondary btn-member" target="_self"
        					     ng-if="((userState == 'none') || (userState == 'exiting'))  && (loggedUser.id > 0)">
        					   	 <em class="fa fa-sign-in-alt"></em>&nbsp;{{txt('ASPIRE')}}
        				</a>
     					<a href="{{LNG.MYDOMAIN}}/opt/members/activate/type/group/id/{{groupId}}/userid/{{loggedUser.id}}/{{csrToken}}/1" 
        					     class="btn btn-secondary btn-member" target="_self"
        					     ng-if="(userState == 'pause')  && (loggedUser.id > 0)">
        					   	 <em class="fa fa-sign-in-alt"></em>&nbsp;{{txt('ACTIVATE')}}
        				</a>
     					<a href="{{LNG.MYDOMAIN}}/opt/members/quit/type/group/id/{{groupId}}/userid/{{loggedUser.id}}/{{csrToken}}/1" 
        					     class="btn btn-secondary btn-member" target="_self" 
        					     ng-if="(userState == 'active') && (loggedUser.id > 0)">
        					   	 <em class="fa fa-sign-out-alt"></em>&nbsp;{{txt('QUIT')}}
        				</a>
     					<a href="{{LNG.MYDOMAIN}}/opt/members/notaspire/type/group/id/{{groupId}}/userid/{{loggedUser.id}}/{{csrToken}}/1" 
        					     class="btn btn-secondary btn-member" target="_self" 
        					     ng-if="(userState == 'active') && (loggedUser.id > 0)">
        					   	 <em class="fa fa-times"></em>&nbsp;{{txt('NOTASPIRE')}}
        				</a>
     					<a href="{{LNG.MYDOMAIN}}/opt/members/pause/type/group/id/{{groupId}}/userid/{{loggedUser.id}}/{{csrToken}}/1" 
        					     class="btn btn-secondary btn-member" target="_self" 
        					     ng-if="(userState == 'active') && (loggedUser.id > 0)">
        					   	 <em class="fa fa-clock"></em>&nbsp;{{txt('PAUSE')}}
        				</a>
    				</div>
              	</p>
              	<p>
              		 <label>
              		   {{txt('DESCRIPTION')}} *:
              		 </label>

              		<textarea id="description" name="description" cols="80" rows="10">{{item.description}}</textarea>
              	</p>
                <p>
                	<div style="display:inline-block">
              		<label>{{txt('STATE')}} *:</label>
              		<select id="state" name="state">
              			<option value="proposal">{{txt('PROPOSAL')}}</option>
              			<option value="active">{{txt('ACTIVE')}}</option>
              			<option value="closed">{{txt('CLOSED')}}</option>
              		</select>
              		</div>
              		&nbsp;
                	<div style="display:inline-block">
              		<label>{{txt('REG_MODE')}} *:</label>
              		<select id="reg_mode" name="reg_mode">
              			<option value="invite">{{txt('REG_MODE_INVITE')}}</option>
              			<option value="admin">{{txt('REG_MODE_ADMIN')}}</option>
              			<option value="self">{{txt('REG_MODE_SELF')}}</option>
              			<option value="candidate">{{txt('REG_MODE_CANDIDATE')}}</option>
              		</select>
              		</div>
              	</p>
              	
              	<div>
              		<label>{{txt('GROUP_TO_ACTIVE')}} *:</label>
              		<input type="text" id="group_to_active" name="group_to_active" min="0" max="100" value="{{item.group_to_active}}" />
              	</div>
              	<div>
              		<label>{{txt('GROUP_TO_CLOSE')}} *:</label>
              		<input type="text" id="group_to_close" name="group_to_close" min="0" max="100" value="{{item.group_to_close}}" />
              	</div>

              	<div>
              		<label>{{txt('MEMBER_TO_ACTIVE')}} *:</label>
              		<input type="text" id="member_to_active" name="member_to_active" min="0" max="100" value="{{item.member_to_active}}" />
              	</div>
              	<div>
              		<label>{{txt('MEMBER_TO_EXCLUDE')}} *:</label>
              		<input type="text" id="member_to_exclude" name="member_to_exclude" min="0" max="100" value="{{item.member_to_exclude}}" />
              	</div>
              	
              	<p>
              		<label>{{txt('AVATAR')}}:</label>
              		<input id="avatar" name="avatar" type="text" value="{{item.avatar}}"  size="50" />
              	</p>
              	<div class="buttons">
              		<br />
              		<button type="button" id="btnOK" class="btn btn-primary">
              			<em class="fa fa-check"></em>{{txt('OK')}}</button>
              		&nbsp;
              		<button type="button" id="btnCancel" class="btn btn-secondary"> 
              			<em class="fa fa-reply"></em>{{txt('CANCEL')}}</button>
              		&nbsp;
              		<button type="button" id="btnBack" class="btn btn-primary">
              			<em class="fa fa-reply"></em>{{txt('OK')}}</button>
              		&nbsp;
               		<button type="button" id="btnCandidate" class="btn btn-secondary">
               			<em class="fa fa-sign-in-alt"></em>{{txt('CANDIDATE')}}</button>
               		&nbsp;
               		<button type="button" id="btnLogin" class="btn btn-secondary">
               			<em class="fa fa-sign-in-alt"></em>{{txt('LOGIN_TO_GROUP')}}</button>
               		&nbsp;
              		<button type="button" id="btnExit" class="btn btn-secondary">
              			<em class="fa fa-sign-out"></em>{{txt('EXIT_FROM_GROUP')}}</button>
              		&nbsp;	
              		<div class="specButtons">	
                  		<button type="button" id="btnAdd" class="btn btn-secondary">
                  			<em class="fa fa-plus-circle"></em>{{txt('ADD_SUB_GROUP')}}</button>
                  		&nbsp;	
                  		<button type="button" id="btnRemove" class="btn btn-danger">
                  			<em class="fa fa-ban"></em>{{txt('REMOVE_GROUP')}}</button>
              		</div>	
              		<br />	
              		<br />	
              	</div>
              </form>
        <?php
    }
    
    /**
     * group adatképernyő
     * @param object $p - $p->item - group record mezői, $p->formTitle, $p->user, 
     *    $p->userid, $p->filterUser
     */
	public function form(Params $p) {
	    
	    $this->echoHtmlHead($p);
        ?>	
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	<div id="divGroupForm">
              	<div class="parents">
              	<?php 
              	for ($i = count($p->parents) - 1; $i >= 0; $i--) {
              	    echo '<span class="fa fa-caret-right"></span>&nbsp;
                          <a href="'.MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->parents[$i]->id.'" target="_self">
                            '.$p->parents[$i]->name.'
                          </a>';
              	}
              	?>
              	</div>
                <h2><em class="fa fa-users"></em>&nbsp;&nbsp;&nbsp;{{txt(formTitle)}}</h2>
    			<div class="alert alert-danger" ng-if="msgs.length > 0">
    				<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
    			</div>
				<?php $this->echoGroupSubMenu($p); ?>
				<?php $this->echoGroupForm($p); ?>
				<?php  
				    include_once './controllers/like.php';
				    $likeController = new LikeController();
				    $likeController->show('group', $p->item->id,'GROUP_LIKE','GROUP_LIKE_HELP')
				?>
            </div><!-- #divGroupForm -->  
		    <div class="clear"></div>
		    <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
		  <?php $this->loadJavaScriptAngular('groups',$p); ?>
        </body>
        </html>
        <?php 		
	}

	
	/**
	 * group törlés megerősítő kérdés
	 * @param object $p -  $p->item = GroupRecord
	 */
	public function deleteGroup(Params $p) {
	    $this->echoHtmlHead($p);
	    foreach ($p->user as $fn => $fv) {
	        $p->$fn = $fv;
	    }
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
        	<div id="removeGroup">  
        		<form action="<?php echo MYDOMAIN; ?>/opt/groups/doremovegroup" 
        		    method="post" target="_self">
        		    <input type="hidden" name="{{csrToken}}" value="1" />
        		    <input type="hidden" name="backUrl" value="{{backUrl}}" />
        		    <input type="hidden" name="groupId" value="{{item.id}}" />
            		<h2>{{txt('REMOVE_GROUP')}}</h2>
            		<p><strong>{{item.name}}</strong></p>
            		<p>{{txt('SURE_REMOVE_GROUP')}}</p>
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
	 * fa struktúrás böngésző
	 * @param object $p (parentGroup, items, offset, limit, orderField, orderDir, filterStr
	 */
	public function browser(Params $p) {
	    
	    $this->echoHtmlHead($p);
	    $backUrl = MYDOMAIN.'/opt/users/list';
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
        	<div id="groupsList">  
        		<form id="formGroupsList" action="<?php echo MYDOMAIN; ?>/opt/groups/list" 
        		    method="post" target="_self">
        		    <input type="hidden" name="{{csrToken}}" value="1" />
        		    <input type="hidden" name="offset" id="offset" value="{{offset}}" />
        		    <input type="hidden" name="limit" id="limit" value="{{limit}}" />
        		    <input type="hidden" name="orderField" id="orderField" value="{{orderField}}" />
        		    <input type="hidden" name="orderDir" id="orderDir" value="{{orderDir}}" />
        		    
        		    <div class="parents">
                  	<?php 
                  	for ($i = count($p->parents) - 1; $i >= 0; $i--) {
                  	    echo '<span class="fa fa-caret-right"></span>&nbsp;
                              <a href="'.MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->parents[$i]->id.'" target="_self">
                                '.$p->parents[$i]->name.'
                              </a>';
                  	}
                  	?>
                  	</div>
        		    
            		<h2><em class="fa fa-users"></em>&nbsp;&nbsp;&nbsp;{{txt(formTitle)}}</h2>
            		<h3>{{txt(formSubTitle)}}</h2>
    				
    				<div class="alert alert-success" ng-if="msgs.length > 0">
    					<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
    				</div>

        			<p class="help"><?php echo txt('GROUPS_LIST_HELP'); ?></p>
        			
   					<?php echo $p->items; ?>
   					
					<p>
    					<a id="addSubGroup" class="btn btn-primary" target="_self"
    					   href="{{MYDOMAIN}}/opt/groups/add/parentid/{{parentId}}/{{csrToken}}/1">
    					   <span class="fa fa-plus-circle"></span>&nbsp;{{txt('ADD_SUB_GROUP')}}
    					</a>
					</p>
        		</form>
			</div>    
		    <div class="clear"></div>
		    <div class="clear"></div>
		    <?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
	      <script type="text/javascript">

	        // le kell tiltani, hogy ugyanaz az em click rutin rövid időn belül kétszer fusson
	        // erre szolgál ez a változó
			global.disabledId = '';

			/**
			* a subgroup nem biztos, hogy be van olvasva (elöször csak egy üres ul kerül kialakitásra)
			* tehát ha az ul-nek nulla gyermek eleme van akkor be kell olvasni az adatbázisból
			* AJAX hívással
			* @param JqueryUlObject subGroup
			* @param int parentId 
			* @param function() success function
			*/
	        function loadSubGroup(subGroup, parentId, successFun) {
		        if (subGroup[0].childElementCount <= 0) {
    		        global.working(true);
    		        // ajax server result: {parentId:###, items:[{id,name,childs:bool}..... ]}
    		        // ajaxhivás(parentId, function(result) {
    		        var url = '<?php echo MYDOMAIN?>/opt/groups/loadsubgroup';
    		        var data = {"parentId": parentId};
    		        global.post(url, data,  function(res) {
        		        		//  res.items elemekkel az ul feltöltése
								var parentId = res.parentId;
								var ul = $('#i_'+parentId+' ul:first');
								for (var i=0; i < res.items.length; i++) {
									var newLi = $('<li id="i_'+res.items[i].id+'"><em></em>'+
											'<var>'+
											'<img class="groupIcon" src="'+res.items[i].avatar+'" />'+
											res.items[i].name+
											'</var></li>');
									ul.append(newLi);
									if (res.items[i].childs) {
										var newUl = '<ul style="display:none"></ul>';
										$('#i_'+res.items[i].id).append(newUl);
									}
								}
			    		        global.working(false);
			    		        treeInit('i_'+parentId);
			    		        successFun();
    		        });
		        } else {
			        successFun();
			    }
		    }

			function treeInit(parentId) {
				if (parentId == undefined) {
					parentId = 'groupsList';
				}
				// em -ek stilusámak beállítása
				var items = $('#'+parentId+' li');
				for (var i = 0; i < items.length; i++) {
					if ((items[i].id != '') & (items[i] != undefined)) {
						item = $('#'+items[i].id);
						if (item.find('ul').length) {
							item.find('em:first').addClass('fa');
							item.find('em:first').addClass('fa-plus-square');
							item.find('em:first').attr('style','cursor:pointer');
						}
					}	
				}
				// em click rutin
				$('#'+parentId+' em').click(function() {
					var itemId = this.parentNode.id;
					if ((itemId == '') | (itemId == undefined) | (itemId == global.disabledId)) {
						return;
					}
					global.disabledId = itemId; 
					var item = $('#'+itemId);
					var subgroup = item.find('ul:first');
					var em = item.find('em:first');
					if (subgroup.is(':hidden')) {
						// a subgroup nem biztos, hogy teljes egészében be van olvasva (elöször csak 2 elemt olvasunk be)
						// theát ha a subgroupnak kevesebb mint 3 gyermek eleme van akkor be kell olvasni az adatbázisból
						// AJAX hívással
						loadSubGroup(subgroup, itemId.substr(2,100), function() {
						     subgroup.show();
						     em.removeClass('fa-add-square');		
						     em.addClass('fa-minus-square');		
						});
						subgroup.show();
						em.removeClass('fa-add-square');		
						em.addClass('fa-minus-square');		
					} else {				
						subgroup.hide();
						em.removeClass('fa-minus-square');		
						em.addClass('fa-plus-square');		
					}
					window.setTimeout('global.disabledId="";',500);			
				});

				// name click rutin
				$('#'+parentId+' var').click(function() {
					var itemId = this.parentNode.id.substr(2,100);
					window.location='<?php echo MYDOMAIN; ?>/opt/groups/groupform/groupid/'+itemId+
					'/<?php echo $p->csrToken; ?>/1';
				});
				
			} // treeInit
			window.setTimeout('treeInit()',1000);
	      </script>
		  <?php $this->loadJavaScriptAngular('groups',$p); ?>
        </body>
        </html>
	    <?php
	}
	
}
?>

