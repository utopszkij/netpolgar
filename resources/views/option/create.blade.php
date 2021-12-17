<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$poll->name = \Request::old('name');
		@endphp
	@endif
	<div class="optionController">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('option.add') }}</h2>
            </div>
        </div>
    </div>
	 <h3>
		<a href="{{ \URL::to('/'.$poll->parent_type.'/'.$poll->parent) }}">
			<em class="fas fa.hand-point-right"></em>
			{{ $parent->name }}
		</a>		 
	 </h3>
	 <h4>
		<a href="{{ \URL::to('/polls/'.$poll->id) }}">
			<em class="fas fa.hand-point-right"></em>
			{{ $poll->name }}
		</a>		 
	 </h4>
 
   <form action="{{ \URL::to('/options') }}" method="POST">
   @csrf
         <input type="hidden" name="pollId" value="{{ $poll->id }}" class="form-control" placeholder="">
         <input type="hidden" name="backUrl" value="{{ $backUrl }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('option.name') }}:</label>
                    <input type="text" name="name" value=""
                    	style="width:500px" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
	     <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('option.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ \URL::previous() }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('option.cancel') }}
	              </a>
	            </div>
	     </div>  
      </div>
    </form>
</x-guest-layout>  
