<x-guest-layout>  
	<div id="likeInfoContainer">
        <div class="row">
            <div class="col-12">
				<h2>{{ $title }}</h2>
				<h3>{{ __('account.details') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
				<table class="table">
					<thead>
						<tr>
							<th>{{ __('account.crated_at') }}</th>
							<th>{{ __('account.value') }}</th>
							<th>{{ __('account.comment') }}</th>
							<th>{{ __('account.partner') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($data as $item)
						<tr>
							<td>{{ $item->created_at }}</td>
							<td>{{ $item->value }}</td>
							<td>{{ $item->comment }}</td>
							<td>{{ $item->partner->name }}</td>
						</tr>
						@endforeach 
					</tbody>
				</table>
				@if (count($data) == 0)
					<div class="row">
						<div class="col-12">
							{{ __('account.notData') }}
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
