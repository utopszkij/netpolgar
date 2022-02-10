<?php
function selected($act, $value) {
		$result = '';
		if ($act == $value) {
			$result = ' checked="checked"';
		}
		return $result;
}
?>

<x-guest-layout>  
	<div id="cartContainer">
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12">
				<h2 style="text-align:center">{{ __('cart.selectCustomerType') }}</h2>
			</div>
		</div>
		<form action="{{ $nextUrl }}" method="get">
			<input type="hidden" name="product_id" value="{{ $product_id }}" />
			<input type="hidden" name="quantity" value="{{ $quantity }}" />
			<div class="row" style="text-align:center">
				<div class="col-12">
						<ul style="display:inline-block; text-align:left; width:auto">
							<li>
								<input type="radio" name="customerType" 
									value="users_{{ \Auth::user()->id }}"
									{!! selected('users_'.\Auth::user()->id, $customerType) !!} />
								{{ __('cart.bySelf') }}
							</li>
							@foreach ($customerTypes as $item)
							<li>
								<input type="radio" name="customerType" 
									value="{{ $item->type.'_'.$item->id }}"
									{!! selected($item->type.'_'.$item->id, $customerType) !!} />
								{{ $item->name }} {{ __('cart.byTeam') }}
							</li>
							@endforeach
						</ul>
				</div>
			</div>
			<div class="row" style="text-align:center">
				<div class="col-12">
					<button type="submit" class="btn btn-primary">
						<em class="fas fa-check"></em>&nbsp;
						{{ _('OK') }}
					</button>
				</div>
			</div>	
		</form>
		
   </div>      

</x-guest-layout>  
