<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$poll->name = \Request::old('name');
		@endphp
	@endif
	<div class="accountController">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('account.send') }}</h2>
            </div>
        </div>
    </div>
	 <h3>
			{{ $fromTitle }}
	 </h3>
 
   <form action="{{ \URL::to('/account/send') }}" method="POST">
   @csrf
         <input type="hidden" name="fromType" value="{{ $fromType }}" />
         <input type="hidden" name="fromId" value="{{ $fromId }}" />
         <input type="hidden" name="backUrl" value="{{ $backUrl }}" />
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('account.targetId') }}:</label>
                    <input type="text" name="targetId" value=""
                    	style="width:500px" class="form-control" placeholder="U... vagy T...">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('account.value') }}:</label>
                    <input type="number" name="value" min="0" max="1000000" value="1"
                    	style="width:200px" class="form-control" />
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('account.comment') }}:</label>
                    <input type="textr" name="comment" 
                    	style="width:600px" class="form-control" />
                </div>
            </div>
         </div>
         
	     <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>
	              		{{ __('account.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ $backUrl }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('account.cancel') }}
	              </a>
	            </div>
	     </div>  
      </div>
    </form>
</x-guest-layout>  
