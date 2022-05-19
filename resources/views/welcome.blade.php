<?php 
// ha csak egyetlen egy user van, akkor az legyen sysadmin
$table = \DB::table('users');
$darab = $table->count();
if ($darab == 1) {
    $user = $table->orderBy('id')->first();
    $table->where('id','=',$user->id);
    $table->update(["current_team_id" => 0]);
}
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	@include('layouts/htmlhead')
    </head>
    <body class="antialiased">
    	<div>
	    	@include('navigation-menu')
    	</div>
		<main class="welcome">		
	    	@include('message')      
			<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
				<div class="carousel-indicators">
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
				</div>
				<div class="carousel-inner">
					<div class="carousel-item active">
					  <img src="{{ URL::to('/') }}/img/slide-1.jpg" class="d-block w-100" alt="...">
					</div>
					<div class="carousel-item">
					  <img src="{{ URL::to('/') }}/img/slide-2.jpg" class="d-block w-100" alt="...">
					</div>
					<div class="carousel-item">
					  <img src="{{ URL::to('/') }}/img/slide-3.jpg" class="d-block w-100" alt="...">
					</div>
					<div class="carousel-item">
					  <img src="{{ URL::to('/') }}/img/slide-4.jpg" class="d-block w-100" alt="...">
					</div>
				</div>
				<button class="carousel-control-prev" type="button" onclick="true"
					data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Previous</span>
				</button>
				<button class="carousel-control-next" type="button" onclick="true"
					data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Next</span>
				</button>
			</div>		
			
			
			<div style="z-index:10; position:absolute; top:300px; width:100%; text-align:center">
				<a href="#description" class="btn btn-primary descriptionBtn" onclick="true"
				    style="opacity:0.6; padding:10px 30px 10px 30px; border-radius:15px">
			 		{{ __('Description') }}
				</a>
			</div> 
			<a name="description" />
			@include('welcome-'.app()->getLocale())
		</main>
        <div>
	        @include('footer')
	    </div>    
    </body>
</html>
