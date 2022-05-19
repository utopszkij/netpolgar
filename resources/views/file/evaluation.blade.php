<x-guest-layout>  
   <div id="file">
    <div class="row">
		<div class="col-3"></div>
        <div class="col-6">
            <div class="pull-left">
                <h2>{{ __('file.evaluation') }}</h2>
            </div>
        </div>
		<div class="col-3"></div>
    </div>
	 <div class="row">
			<div class="col-3"></div>
			<div class="col-6">
				 <h3>{{ $file->name }}</h3>
            </div>
			<div class="col-3"></div>
 	</div>
 	<form method="post" action="{{ \URL::to('/files/evaluation') }}" class="form">
	@csrf	
	<input type="hidden" name="fileId" value="{{ $file->id }}" />	
	<input type="hidden" name="backUrl" value="{{ $backUrl }}" />	
	<div class="row">
		<div class="col-3"></div>
		<div class="col-6">
			<input type="radio" class="form-control" name="evaluation" value="1" />
			{{ __('file.evaluation1') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="2" />
			{{ __('file.evaluation2') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="3" />
			{{ __('file.evaluation3') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="4" />
			{{ __('file.evaluation4') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="5" />
			{{ __('file.evaluation5') }} <br />
			<br />
			<button type="submit" class="btn btn-primary">
				<em class="fas fa-check"></em>
				{{ __('file.ok') }}
			</button>	
		</div>
		<div class="col-3"></div>
	</div>	
	</form>
 
    
</x-guest-layout>  
