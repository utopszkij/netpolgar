
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

<section class="section-table cid-s3RbsEKWTB" id="table1-15">
    <div class="container">
        <ul class="inner_menu">
    		<li><a href="#">Tagok</a></li>
    		<li><a href="#">Alcsoportok</a></li>
    		<li><a href="#">Projektek</a></li>
    		<li><a href="#">Piactér</a></li>
    		<li><a href="#">Dokumentumok</a></li>
    		<li><a href="#">Viták</a> {{commentCount.total}}
    		   <span ng-if="commentCount.new > 0">/<strong>{{commentCount.new}}</strong></span>
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