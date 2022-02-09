<x-guest-layout>  

    <div class="row">
        <div class="col-lg-12 margin-tb">
                <h2>{{ __('order.item') }}</h2>
        </div>
    </div>
 
    <form action="{{ \URL::to('/order/doconfirm') }}" method="POST">
		@csrf
         <input type="hidden" name="orderItemId" value="{{ $orderItem->id }}" class="form-control" />
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.orderId') }}:</label>
                    {{ $order->id }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.producer') }}:</label>
                    {{ $producer->name }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.product') }}:</label>
                    {{ $product->name }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.quantity') }}:</label>
                    {{ $orderItem->quantity }} {{ $product->unit }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.price') }}:</label>
                    {{ round($orderItem->quantity * $product->price * 10) / 10 }} NTC
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.date') }}:</label>
                    {{ $orderItem->created_at }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.oldStatus') }}:</label>
                    {{ __('order.'.$orderItem->status) }}
                </div>
            </div>
         </div>
         
         <p>Userstatus: {{ $userStatus }}</p>
         @if ((strpos($userStatus,'customer/') > 0) | (substr($userStatus,0,9) == 'producer/'))
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.newStatus') }}:</label>
                    <select name="newStatus" id="newStatus">
						<option value="{{ $orderItem->status }}">
							{{ __('order.'.$orderItem->status) }}
						</option>
						@if (substr($userStatus,0,9) == 'producer/')
							@if ($orderItem->status == 'ordering')
								<option value="confirmed">
									{{ __('order.confirmed') }}
								</option>
								<option value="denied">
									{{ __('order.denied') }}
								</option>
							@endif
							@if ($orderItem->status == 'confirmed')
								<option value="closed1">
									{{ __('order.closed1') }}
								</option>
								<option value="denied">
									{{ __('order.denied') }}
								</option>
							@endif
						@endif
						@if (strpos($userStatus,'customer/') > 0) {
							@if ($orderItem->status == 'ordering')
								<option value="canceled">
									{{ __('order.canceled') }}
								</option>
							@endif
							@if ($orderItem->status == 'confirmed')
								<option value="closed2">
									{{ __('order.closed2') }}
								</option>
								<option value="canceled">
									{{ __('order.canceled') }}
								</option>
							@endif
							@if ($orderItem->status == 'closed1')
								<option value="closed2">
									{{ __('order.closed2') }}
								</option>
							@endif
						@endif
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('order.msg') }}:</label>
                    <textarea name="msg" id="msg" cols="80" rows="5"></textarea> 
                </div>
            </div>
         </div>
         @endif
         <div class="row">
	            <div class="col-12">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('order.save') }}
	              </button>
	              <a class="btn btn-secondary" 
	              		href="{{ \URL::to('/') }}">
	                  <em class="fas fa-ban"></em>
	                  Mégsem
	              </a>
	            </div>
	         </div>  
        </div>
   
    </form>
    
    <script type="text/javascript">
    $(function() {
		$('#newStatus').change(function() {
			var s = $('#msg').html();
			var newStatus = $('#newStatus').val();
			if (newStatus == 'confirmed') {
				s = "új státusz: visszaigazolt\n"+s;
			} else if (newStatus == 'denied') {	
				s = "új státusz: visszautasított\n"+s;
			} else if (newStatus == 'closed1') {	
				s = "új státusz: átadás folyamatban\n"+s;
			} else if (newStatus == 'closed2') {	
				s = "új státusz: átadott, átvett\n"+s;
			} else if (newStatus == 'canceled') {	
				s = "új státusz: visszavont\n"+s;
			} else if (newStatus == 'ordering') {	
				s = "új státusz: megrendelt\n"+s;
			}	
			$('#msg').html(s);
		});
	})
    </script>
</x-guest-layout>  
