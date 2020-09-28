<section class="testimonials1 cid-s2WNFGTaId" id="testimonials1-b">
 
    <div class="container">
        <div class="media-container-row">
            <div class="title col-12 align-center">
                <h2 class="pb-3 mbr-fonts-style display-2">Legújabb tagjaink</h2>
            </div>
        </div>
    </div>

    <div class="container pt-3 mt-2">
        <div class="media-container-row">
            <div class="mbr-testimonial p-3 align-center col-12 col-md-6 col-lg-4" ng-repeat="item in newUsers">
                <div class="panel-item p-3">
                	<a href="{{MYDOMAIN}}/opt/users/form/id/{{item.id}}" target="_self">
                        <div class="card-block">
                            <div class="testimonial-photo">
                                <img src="{{item.avatar}}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="mbr-author-name mbr-bold mbr-fonts-style display-7">
                               {{item.nick}}
    						</div>
                            <small class="mbr-author-desc mbr-italic mbr-light mbr-fonts-style display-7">
                               {{item.pubinfo}}
                            </small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>   
</section>
