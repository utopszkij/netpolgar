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
        @if ($poll->config->secret == 0) 
            <h4>Nyilt szavazás</h4>
            <thead>
                <tr>
                    <th>{{ __('vote.name') }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                <tr>
                    <td>
                        @if ($value->accreditedName != '')
                            {{ $value->userName }} --> {{ $value->accreditedName}}
                        @else
                            {{ $value->userName }}
                        @endif
                    </td>
                    <td>{{ $value->position }}</td>
                    <td>{{ $value->name }}</td>
                </tr>
                @endforeach
            </tbody>
        @else
            <h4>Titkos szavazás</h4>
            <thead>
                <tr>
                    <th>{{ __('vote.ballot_id') }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                <tr>
                    <td>
                        {{ $value->ballot_id }}
                    </td>
                    <td>{{ $value->position }}</td>
                    <td>{{ $value->name }}</td>
                </tr>
                @endforeach
            </tbody>
        @endif
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
			<a class="btn btn-secondary" 
				href="{{ \URL::to('/'.$poll->id.'/votes/csv') }}"
				onclick="csvClick()">
				CSV
			</a> 		
		</div>    
    </div>
    
    <script>
		function csvClick() {
			$('#waiting').show();
			setTimeout('$("#waiting").hide()',5000);
		}
    </script>
    
  </div>        
</x-guest-layout>  
