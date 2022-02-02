<x-guest-layout>  
	<div id="accountContainer">
        <div class="row">
            <div class="col-12">
				@if ($actorType == 'teams') 
					<a href="{{ \URL::to('/teams/'.$actorId) }}">
				@else
					<a href="{{ \URL::to('/member/user/'.$actorId) }}">
				@endif
				<h2><em class="fas fa-hand-point-right"></em>{{ $title }}</h2>
				</a>
				<h3>{{ __('account.details') }}</h3>
				<h4>{{ __('account.ID') }}:{{ $accountId }}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
				<h2>{{ __('account.ballance') }}: {{ $ballance }} NTC</h2>
			</div>	
		</div>	
        <div class="row">
            <div class="col-12">
				<table class="table">
					<thead>
						<tr>
							<th>{{ __('account.created_at') }}</th>
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
			@if ($userAdmin)
            <div class="row">
				<div class="col-12">
					<a class="btn btn-primary" href="{{ \URL::to('/account/send/'.$accountId) }}">
						<em class="fas fa-money-bill"></em><em class="fas fa-arrow-right"></em>
						{{ __('account.send') }}
					</a>
				</div>
			</div>		
			@endif
        </div>
  </div>        
</x-guest-layout>  
