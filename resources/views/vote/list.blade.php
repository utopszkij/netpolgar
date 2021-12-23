<x-guest-layout>  
	<div id="voteListContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('vote.list') }}</h2>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
        		<h2>{{ $poll->name }}</h2>
		  </div>
	 </div>	  

     
    <table class="table table-bordered">
        <tr>
            <th>{{ __('vote.ballotId') }}</th>
            <th></th>
            <th>{{ __('vote.name') }}</th>
        </tr>
        @foreach ($data as $key => $value)
        <tr>
            <td>{{ $value->ballot_id }}</td>
            <td>{{ $value->position }}</td>
            <td>{{ \Str::limit($value->name, 50) }}</td>
        </tr>
        @endforeach
    </table>
    @if (count($data) == 0)
    	<div>{{ __('vote.voteNotFound') }}</div>
    @endif  
    {!! $data->links('pagination') !!}
    <div class="row">&nbsp;</div>
    <div class="row">
		<div class="col-12">
			<a class="btn btn-primary" href="{{ \URL::to('/polls/'.$poll->id) }}">
				{{ __('vote.back') }}
			</a>
			&nbsp; 		
			<a class="btn btn-secondary" href="{{ \URL::to('/'.$poll->id.'/votes/csv') }}">
				CSV
			</a> 		
		</div>    
    </div>
    
  </div>        
</x-guest-layout>  
