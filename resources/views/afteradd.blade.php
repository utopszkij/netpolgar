<x-guest-layout>  
	<div id="cartContainer">
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12">
				<h2>{{ __('cart.succesadd') }}</h2>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12" style="text-align:center">
				<a class="btn btn-primary" href="{{ \URL::to('/products/list/0') }}">
					{{ __('product.back_products') }}
				</a>
				&nbsp;&nbsp;
				<a class="btn btn-secondary" href="{{ \URL::to('/carts/list') }}">
					{{ __('product.order_finish') }}
				</a>
				&nbsp;&nbsp;
				<a class="btn btn-danger" href="{{ \URL::to('/carts/clear') }}">
					{{ __('product.order_cancel') }}
				</a>
			</div>
		</div>
		
   </div>        
</x-guest-layout>  
