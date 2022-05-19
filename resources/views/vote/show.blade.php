<x-guest-layout>  
	<div id="voteGetForm">
	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
		@endphp
	@endif
	<div id="formShow">   
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	<h2>
            		<em class="fas fa-balance-scale"></em>
                	{{ __('vote.getMyVote') }}
               </h2>
            </div>
        </div>
    </div>
 
	<h3>
		<a href="{{ \URL::to('/poll/'.$poll->id) }}">
			<em class="fas fa-hand-point-right"></em>
			{{ $poll->name }}
		</a>
	</h3>
	<div class="row">
		<div class="col-12">
			@if (count($votes) > 0)
				<ul>
				@foreach($votes as $vote)
					@if ($poll->config->pollType == 'yesno')
						@if ($cote->position == 1)
							<li>IGEN - {{ $vote->name }}</li>
						@else
							<li>NEM - {{ $vote->name }}</li>
						@endif	
					@endif
					@if ($poll->config->pollType == 'onex')
						<li>{{ $vote->name }}</li>
					@endif
					@if ($poll->config->pollType == 'morex')
						<li>{{ $vote->name }}</li>
					@endif
					@if ($poll->config->pollType == 'pref')
						<li>{{ (1 + $vote->position) }}. {{ $vote->name }}</li>
					@endif
				@endforeach	
				</ul>
			@else
				{{ __('vote.voteNotFound') }}			
			@endif
		</div>
	</div>
	<div class="row">&nbsp;</div>
	<div class="row">
		<div class="col-12">
			<a class="btn btn-primary" href="{{ \URL::to('/polls/'.$poll->id) }}">
					{{ __('vote.back') }}
			</a>
		</div>	
	</div>
 </div>
    
</x-guest-layout>  
