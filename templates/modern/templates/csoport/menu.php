
<div class="parents">
  		<span ng-repeat="parent in parents | orderBy:'-'">
  	    <em class="fa fa-caret-right"></em>&nbsp;
        <a href="{{MYDOMAIN}}/opt/groups/form/id/{{parent.id}}" target="_self">
                {{parent.name}}
        </a>
        </span>
</div>

<div class="alert alert-{{msgClass}}" ng-if="msgs.length > 0">
	<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#table1-15"
        aria-controls="teabl1-15"
        aria-expanded="false" aria-label="Toggle navigation">
        <em class="fa fa-ellipsis-v"></em>
    </button>
    <section class="section-table cid-s3RbsEKWTB collapse navbar-collapse" id="table1-15">
        <div class="container">
            <ul class="inner_menu">
        		<li><a href="{{MYDOMAIN}}/opt/members/list/type/groups/id/{{item.id}}" target="_self">Tagok</a></li>
        		<li><a href="{{MYDOMAIN}}/opt/groups/list/parentid/{{item.id}}/userid/0" target="_self">Alcsoportok</a></li>
        		<li><a href="{{MYDOMAIN}}/opt/projects/list/type/groups/id/{{item.id}}" target="_self">Projektek</a></li>
        		<li><a href="{{MYDOMAIN}}/opt/markets/list/type/groups/id/{{item.id}}" target="_self">Piactér</a></li>
        		<li><a href="{{MYDOMAIN}}/opt/files/list/type/groups/{{item.id}}" target="_self">Dokumentumok</a></li>
        		<li><a href="{{MYDOMAIN}}/opt/comments/type/groups/id/{{item.id}}" target="_self">Viták</a> 
        		   {{commentCount.total}}<span ng-if="commentCount.new > 0">/<strong>{{commentCount.new}}</strong></span>
        		</li>
        		<li><a href="#">Szavazások</a> 
        			{{pollCount.total}}<span ng-if="pollCount.new > 0">/<strong>{{pollCount.new}}</strong></span>                        
        		</li>
        		<li><a href="#">Események</a>
        			{{eventCount.total}}<span ng-if="eventCount.new > 0">/<strong>{{eventCount.new}}</strong></span>
        		</li>
        		<li><a href="#">Privát üzenetek</a>
    				{{messageCount.total}}<span ng-if="messageCount.new > 0">/<strong>{{messageCount.new}}</strong></span>    		
        		</li>
            </ul>
        </div>
    </section>
</nav>