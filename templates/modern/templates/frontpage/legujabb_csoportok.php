<section class="features18 popup-btn-cards cid-s2WPxxa1sN" id="features18-f">
   
    <div class="container">
        <h2 class="mbr-section-title pb-3 align-center mbr-fonts-style display-2">Legújabb csoportok</h2>
    	<div class="media-container-row pt-5 ">
			<div class="card p-3 col-12 col-md-6 col-lg-4" ng-repeat="item in newGroups">
                <div class="card-wrapper ">
                    <div class="card-img">
                        <div class="mbr-overlay"></div>
                        <div class="mbr-section-btn text-center">
                        	<a href="{{MYDOMAIN}}/opt/groups/form/id/{{item.id}}" class="btn btn-primary display-4" target="_self">
                        		Tudj meg többet
                        	</a>
                        </div>
                        <img src="{{item.avatar}}" alt="">
                    </div>
                    <div class="card-box">
                        <h4 class="card-title mbr-fonts-style display-7">
                            {{item.name}}</h4>
                        <p class="mbr-text mbr-fonts-style align-left display-7">
                            {{item.description}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>