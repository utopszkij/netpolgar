<x-guest-layout>  
	<div id="cartContainer">
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12">
				<h2 style="text-align:center">{{ __('cart.success_add') }}</h2>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12" style="text-align:center">
				<a class="btn btn-primary" href="{{ \Request::session()->get('productsListUrl') }}">
					<em class="fas fa-reply"></em>
					{{ __('cart.back_products') }}
				</a>
				&nbsp;&nbsp;
				<a class="btn btn-secondary" href="{{ \URL::to('/carts/list') }}">
					{{ __('cart.order_finish') }}
				</a>
				&nbsp;&nbsp;
				<a class="btn btn-danger" href="#" onclick="clearClick()">
					<em class="fas fa-eraser"></em>
					{{ __('cart.order_cancel') }}
				</a>
			</div>
		</div>
		
   </div>      

   <script>
   	function clearClick() { 
			popupConfirm("{{ __('cart.sure_clear') }}", 
				function() {
					location = "{{ \URL::to('/carts/clear') }}";				
				}, 
				true);
		}	   
   </script>  
</x-guest-layout>  
