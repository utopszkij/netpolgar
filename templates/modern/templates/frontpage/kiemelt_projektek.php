<section class="features15 cid-s2WLsELO8g" id="features15-7">
   
    <div class="container">
        <h2 class="mbr-section-title pb-3 align-center mbr-fonts-style display-2">Legújabb projektek</h2>

        <div class="media-container-row container pt-5 mt-2">

            <div class="col-12 col-md-6 mb-4 col-lg-4" ng-repeat="item in newProjects">
               	<a href="{{MYDOMAIN}}/opt/projects/form/id/{{item.id}}" target="_self">
                <div class="card flip-card p-5 align-center">
                        <div class="card-front card_cont">
                            <img src="{{item.avatar}}" alt="">
                        </div>
                        <div class="card_back card_cont">
                            <h4 class="card-title display-5 py-2 mbr-fonts-style">
                            	  {{item.name}}
                            </h4>
                            <p class="mbr-text mbr-fonts-style display-7">
                                {{item.description}}
                            </p>
                        </div>
                </div>
                </a>
            </div>

</div>

</section>