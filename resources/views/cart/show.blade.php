<?php
use App\Models\Minimarkdown;
?>

<x-guest-layout>  
	<div id="cartContainer">
		<p>&nbsp;</p>
		<div class="row">
			<div class="col-12">
				<h2 style="text-align:center">{{ __('cart.show') }}</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<h3 style="text-align:center">{{ $customerName }}</h3>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				{{ __('cart.ballance') }}: <strong>{{ $ballance }}</strong> NTC
			</div>
		</div>
		
		@if (count($items) > 0)
		<div class="row">
			<div class="col-12">
			<table class="table" style="width:100%">
				<thead>
					<tr>
						<th style="width:15%">{{ __('cart.quantity') }}</th>
						<th style="width:50%">{{ __('cart.product') }}}</th>
						<th style="width:15%; text-align:right">{{ __('cart.price') }}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($items as $item)
						<tr>
							<td style="width:15%">{{ $item->quantity }} {{ $item->unit }}
							<td style="width:50%">
								<img src="{{ $item->avatar }}"
									 style="display:inline-block; width:auto; height:32px" />
								{{ $item->name }}
								<a href="#"
									title="{{ __('cart.details') }}"
								   onclick="descClick({{ $item->id }})">
									<em class="fas fa-caret-down"></em>
								</a>
								<div id="desc_{{ $item->id }}"
									class="product" 
									style="display:none">
									{!! Minimarkdown::minimarkdown($item->description) !!}
								</div>							
							</td>
							<td style="width:15%; text-align:right">{{ $item->price }}</td>
							<td style="width:15%"">
								<a class="btn btn-danger" 
									href="#" onclick="itemDelClick({{ $item->id }})">
									<em class="fas fa-ban"></em>{{ __('cart.delete') }} 
								</a>							
							</td>	
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td>{{ __('cart.total') }}</td>
						<td style="text-align:right"><strong>{{ $totalPrice }}</strong></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<a class="btn btn-primary" 
					href="{{ \URL::to('/carts/send') }}">
					<em class="fas fa-paper-plane"></em>{{ __('cart.send') }}
				</a>
				&nbsp;
				<a class="btn btn-secondary" 
					href="{{ \URL::to('/products/list/0') }}">
					<em class="fas fa-reply"></em>{{ __('cart.back_products') }}
				</a>
				
			</div>
		</div>							
		@else
		<div class="row">
			<div class="col-12" style="text-align:center">
				{{ __('cart.empty') }}
				&nbsp;
				<a class="btn btn-secondary" 
					href="{{ \URL::to('/products/list/0') }}">
					<em class="fas fa-reply"></em>{{ __('cart.back_products') }}
				</a>
			</div>
		</div>	
		@endif
   </div>      

   <script>
   	function descClick(i) {
			$('#desc_'+i).toggle();
			return false;   	
   	}
   	function itemDelClick(id) {
   		popupConfirm("{{ __('cart.sureItemDel') }}", 
   			function() {
					window.location = "{{ \URL::to('/carts') }}/"+id+"/delete";   			
   			}, 
   			true);
   		return false;
   	}
   </script>  
</x-guest-layout>  
