<section class="mbr-section content8 cid-s3Ro6ty7kw" id="content8-16">
    <div class="container">
        <div class="media-container-row title">
            <div class="col-12 col-md-8">
                <div class="mbr-section-btn align-center" ng-if="item.id > 0">
    				<button id="likeUpBtn" type="button" class="btn btn-info">
    					{{txt('LIKE')}}
    					<em class="fa fa-thumbs-up"></em>
    					<em class="fa fa-check"></em>
    					&nbsp;<var>{{likeCount.up}}</var>
    				</button>
    				<button id="likeDownBtn" type="button" class="btn btn-info">
    					<span ng-if="item.state != 'active'">{{txt('DISLIKE')}}</span>
    					<span ng-if="item.state == 'active'">{{txt('SUGGEST_CLOSING')}}</span>
    					<em class="fa fa-thumbs-down"></em>
    					<em class="fa fa-check"></em>
    					&nbsp;<var>{{likeCount.down}}</var>
    				</button>	
				</div>
            </div>
        </div>
    </div>
</section>
<div style="display:none">{{onload()}}</div>
