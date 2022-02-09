<x-guest-layout>  

   <div id="product">
    <div class="row">
		<div class="col-3"></div>
        <div class="col-6">
            <div class="pull-left">
                <h2>{{ __('product.evaluation') }}</h2>
            </div>
        </div>
		<div class="col-3"></div>
    </div>
	 <div class="row">
			<div class="col-3"></div>
			<div class="col-6>
				 <h3>{{ $product->name }}</h3>
			 	@if ($product->avatar != "") 
					 <img src="{{ $product->avatar}}"
					 style="width:15%; margin:10px; float:right" />
			 	@endif
            </div>
			<div class="col-3"></div>
 	</div>
 	<form method="post" action="{{ \URL::to('/products/evaluation') }}" class="form">
	@csrf	
	<input type="hidden" name="productId" value="{{ $product->id }}" />	
	<input type="hidden" name="backUrl" value="{{ $backUrl }}" />	
	<div class="row">
		<div class="col-3"></div>
		<div class="col-6">
			<input type="radio" class="form-control" name="evaluation" value="1" />
			{{ __('product.evaluation1') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="2" />
			{{ __('product.evaluation2') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="3" />
			{{ __('product.evaluation3') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="4" />
			{{ __('product.evaluation4') }} <br />
			<input type="radio" class="form-control" name="evaluation" value="5" />
			{{ __('product.evaluation5') }} <br />
			<button type="submit" class="btn btn-primary">
				<em class="fas fa-check"></em>
				{{ __('product.ok') }}
			</button>	
		</div>
		<div class="col-3"></div>
	</div>	
	</form>
 
    
</x-guest-layout>  
