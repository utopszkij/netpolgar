<section class="mbr-section form1 cid-s3Rievtb1N" id="divGroupForm" class="pageBody">
  
    <div class="container">
        <div class="row justify-content-center">
        	<div class="alert col-12">
        		<p ng-repeat="msg in msgs">
        			{{msg}}
        		</p>
        	</div>
            <div class="title col-12 col-lg-8">
                <h2 class="mbr-section-title align-center pb-3 mbr-fonts-style display-2">Csoport tulajdonságok</h2>
            </div>
        </div>
    </div>
	
    <div class="container">
        <div class="row justify-content-center">
            <div class="media-container-column col-12" data-form-type="formoid">
                <form action="index.php" id="formGroupForm" method="POST" class="mbr-form form-with-styler">
                	<input type="hidden" name="option" value="groups" />
                	<input type="hidden" name="task" value="save" />
                	<input type="hidden" name="id" value="{{item.id}}" />
                	<input type="hidden" name="parent_id" value="{{item.parent_id}}" />
                    <div class="row">
                    </div>
                    <div class="dragArea row">
                    	<div class="col-sm-12 col-md-2">
                    		<img src="{{item.avatar}}" ng-id="item.avatar != ''" />
                    	</div>
                        <div class="col-md-4 col-sm-12 form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            {{txt('NAME')}} *</label>
                            <input type="text" name="name" data-form-field="Name" required="required" class="form-control display-7"
                                id="name" 
                            	value="{{item.name}}">
                        </div>
                        <div class="col-md-3 col-sm-12  form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            	{{txt('STATE')}}</label>
                            <select name="state" id="state" class="form-control display-7">
		              			<option value="proposal">{{txt('PROPOSAL')}}</option>
        		      			<option value="active">{{txt('ACTIVE')}}</option>
              					<option value="closed">{{txt('CLOSED')}}</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12  form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            {{txt('GROUP_TO_ACTIVE')}} *</label>
                            <input type="text" name="group_to_active" class="form-control display-7" id="group_to_active"
                            	 value="{{item.group_to_active}}">
                        </div>
                        <div class="col-md-4 col-sm-12  form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                                {{txt('GROUP_TO_CLOSE')}} *</label>
                            <input type="text" name="group_to_close" required="required" class="form-control display-7" 
                            	 id="name-form1-14" value="{{item.group_to_close}}">
                        </div>
                        <div class="col-md-4 col-sm-12 form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            	{{txt('MEMBER_TO_ACTIVE')}} *</label>
                            <input type="text" name="member_to_active" required="required" class="form-control display-7" 
                            	id="meber_to_active"
                            	 value="{{item.member_to_active}}">
                        </div>
                        <div class="col-md-4 col-sm-12  form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            	{{txt('MEMBER_TO_EXCLUDE')}} *</label>
                            <input type="text" name="member_to_exclude" class="form-control display-7"
                            	 id="member_to_exclude" value="{{item.member_to_exclude}}">
                        </div>
                        <div data-for="phone" class="col-md-4 col-sm-12 form-group">
                            <label class="form-control-label mbr-fonts-style display-7">
                            {{txt('AVATAR')}}</label>
                            <input type="text" name="avatar" class="form-control display-7" id="avatar" 
                            	value="{{item.avatar}}">
                        </div>
                        <div class="col-md-4 col-sm-12  form-group">
                      		<label class="form-control-label mbr-fonts-style display-7" 
                      		    ng-if="loggedUser.id > 0">{{txt('USERSTATE')}}: {{txt(userState)}}</label>
                      		<br />    
                    		<a href="{{MYDOMAIN}}/opt/members/aspire/type/groups/id/{{item.id}}/user_id/{{loggedUser.id}}/{{csrToken}}/1" 
                    			     class="btn btn-secondary btn-form display-4" target="_self"
                    			     ng-if="(item.state == 'active') && ((userState == 'none') || (userState == 'exiting'))  && (loggedUser.id > 0)">
                    			   	 <em class="fa fa-sign-in-alt"></em>&nbsp;{{txt('ASPIRE')}}
                    		</a>
                    		<a href="{{MYDOMAIN}}/opt/members/quit/type/groups/id/{{groupId}}/user_id/{{loggedUser.id}}/{{csrToken}}/1" 
                    			     class="btn btn-secondary btn-form display-4" target="_self" 
                    			     ng-if="(item.state == 'active') && (userState == 'active') && (loggedUser.id > 0)">
                    			   	 <em class="fa fa-sign-out-alt"></em>&nbsp;{{txt('QUIT')}}
                    		</a>
                    		<a href="{{MYDOMAIN}}/opt/members/notaspire/type/groups/id/{{groupId}}/user_id/{{loggedUser.id}}/{{csrToken}}/1" 
                    			     class="btn btn-secondary btn-form display-4" target="_self" 
                    			     ng-if="(item.state == 'active') && (userState == 'aspre') && (loggedUser.id > 0)">
                    			   	 <em class="fa fa-times"></em>&nbsp;{{txt('NOTASPIRE')}}
                    		</a>
                    		<a href="{{MYDOMAIN}}/opt/members/pause/type/groups/id/{{groupId}}/user_id/{{loggedUser.id}}/{{csrToken}}/1" 
                    			     class="btn btn-secondary btn-form display-4" target="_self" 
                    			     ng-if="(item.state == 'active') && (userState == 'active') && (loggedUser.id > 0)">
                    			   	 <em class="fa fa-clock"></em>&nbsp;{{txt('PAUSE')}}
                    		</a>
                    		<a href="{{MYDOMAIN}}/opt/members/activate/type/groups/id/{{groupId}}/user_id/{{loggedUser.id}}/{{csrToken}}/1" 
                    			     class="btn btn-secondary btn-form display-4" target="_self" 
                    			     ng-if="(item.state == 'active') && (userState == 'paused') && (loggedUser.id > 0)">
                    			   	 <em class="fa fa-clock"></em>&nbsp;{{txt('ACTIVATE')}}
                    		</a>

                        </div>
                        <div class="col-md-12 form-group">
                            <label class="form-control-label mbr-fonts-style display-7">{{txt('DESCRIPTION')}}</label>
                            <textarea name="description" class="form-control display-7" 
                            	id="description">{{item.description}}</textarea>
                        </div>
                        
                        <div class="col-md-12 input-group-btn align-center">
                            <button type="submit" class="btn btn-primary btn-form display-4">{{txt('OK')}}</button>
                        </div>
                    </div>
                </form>
                
                
				
            </div>
        </div>
    </div>
    
    
</section>