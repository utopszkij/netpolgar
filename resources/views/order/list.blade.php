<x-guest-layout>  
	<div id="orderListContainer">
        <div class="row">
            <div class="col-12">
				<h2>{{ $title }}</h2>
				@if ($producer->name != '')
				<h3>{{ __('order.producer') }}: {{ $producer->name }}
				</h3>
				<a href="{{ \URL::to('/orders/list?customer_type='.$producerType.'&customer='.$producerId) }}">
					<em class="fas fa-hand-point-right"></em>
					{{ __('order.sended') }}
				</a>
				@endif
				@if ($customer->name != '')
				<h3>{{ __('order.customer') }}: {{ $customer->name }}
				</h3>
				<a href="{{ \URL::to('/orders/list?producer_type='.$customerType.'&producer='.$customerId) }}">
					<em class="fas fa-hand-point-right"></em>
					{{ __('order.received') }}
				</a>
				@endif
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
							<th>{{ __('order.customer') }}<br />
								{{ __('order.producer') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($data as $item)
						@php
							$itemCustomer = \DB::table($item->customer_type)
								->where('id','=',$item->customer)->first();
							$itemProducer = \DB::table($item->parent_type)
								->where('id','=',$item->parent)->first();	
						@endphp
						<tr>
							<td>{{ $item->orderId }}</td>
							<td>{{ $item->name }}</td>
							<td>{{ $item->quantity }}</td>
							<td>{{ $item->unit }}</td>
							<td>{{ $item->created_at }}<br />
								{{ __('order.'.$item->status) }}</td>
							<td>{{ $itemCustomer->name }}<br />
								{{ $itemProducer->name }}</td>	
							<td>
								<a href="{{ \URL::to('/orders/'.$item->id.'/confirm') }}" 
									class="btn btn-primary">
									<em class="fas fa-edit"></em>
									{{ __('order.edit') }}
								</a>
							</td>
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
