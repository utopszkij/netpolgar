<x-guest-layout>  
	<div id="voteGetForm">
	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
		@endphp
	@endif
   
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
   <form id="formGetVote" action="{{ \URL::to('/votes/show') }}" method="post">
   @csrf
      <input type="hidden" name="poll_id" value="{{ $poll->id }}" />
	   <div class="row">
	   	<div class="col-12">
	         <label>{{ __('vote.ballotId') }}</label>
	         <input name="ballot_id" type="text" 
	         class="form-control" style="width:300px" value="" />
	      </div>
	   </div>
	   <div class="row">&nbsp;</div>
	   <div class="row>"
	   	<div class="col-12">      
	         <button type="submit" class="btn btn-primary">
	        		<em class="fas fa-check"></em>{{ __('vote.send') }}
	         </button>
	         <a class="btn btn-secondary" href="{{ \URL::previous() }}">
	             <em class="fas fa-ban"></em>
	             {{ __('vote.cancel') }}
	         </a>
	      </div>   
		</div>			 
    </form>
    </div>
    
</x-guest-layout>  
