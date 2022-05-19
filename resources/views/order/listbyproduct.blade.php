<x-guest-layout>  
	<div id="likeInfoContainer">
        <div class="row">
            <div class="col-12">
				<h2>	
					<a href="{{ \URL::to('/products/'.$product->id) }}">
						<em class="fas fa-hand-point-right"></em>
						{{ $product->name }}
					</a>
				</h2>
				<h3>{{ __('order.stockChanges') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
				<table class="table">
					<thead>
						<tr>
							<th>{{ __('order.orderId') }}</th>
							<th>{{ __('order.product') }}</th>
							<th>{{ __('order.quantity') }}</th>
							<th>{{ __('order.unit') }}</th>
							<th>{{ __('order.date') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($data as $item)
						<tr>
							<td>{{ $item->orderId }}</td>
							<td>{{ $item->name }}</td>
							<td>{{ $item->quantity }}</td>
							<td>{{ $item->unit }}</td>
							<td>{{ $item->created_at }}</td>
						</tr>
						@endforeach 
					</tbody>
				</table>
				@if (count($data) == 0)
					<div class="row">
						<div class="col-12">
							{{ __('order.notData') }}
						</div>
					</div>		
				@endif
            </div>
            <div class="row">
				<div class="col-12">
					{!! $data->links('pagination') !!}
				</div>
			</div>		
        </div>
  </div>        
</x-guest-layout>  
