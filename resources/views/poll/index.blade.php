<x-guest-layout>  
	<div id="pollContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	<p>&nbsp;</p>
            	<h2>
            		<a href="{{ \URL::to('/'.$parentType.'/'.$parent->id) }}">
            		<em class="fas fa-hand-point-right"></em>
            		{{ $parent->name }} {{ __('poll.'.$parentType) }}
            		</a>
            	</h2>
            	@if ($statuses == 'proposal-debate')
	                <h3>{{ __('poll.debates') }}</h3>
	            @endif    
            	@if ($statuses == 'vote')
	                <h3>{{ __('poll.vote') }}</h3>
            	@endif
            	@if ($statuses == 'closed')
	                <h3>{{ __('poll.closed') }}</h3>
            	@endif
            </div>
        </div>
    </div>

    @if (($parent->status == 'active') & 
         ($statuses == 'proposal-debate') &
         ($userMember))  
    <div class="row buttons">
       <a class="btn btn-primary" 
         href="{{ \URL::to('/'.$parentType.'/'.$parent->id.'/proposal-debate/polls/create') }}">
        	<em class="fas fa-plus"></em>
        	{{ __('poll.add') }}
       </a>
    </div>
    @endif
     
    <table class="table table-bordered">
        <tr>
            <th>{{ __('poll.status') }}</th>
            <th>{{ __('poll.name') }}</th>
            <th>{{ __('poll.description') }}</th>
        </tr>
        @foreach ($data as $key => $value)
        <tr>
            <td>{{ __('poll.'.$value->status) }}</td>
            <td>
            	<a href="{{ \URL::to('/polls/'.$value->id) }}">
            	{{ $value->name }}
            	</a>
            </td>
            <td>{{ \Str::limit($value->description, 100) }}</td>
        </tr>
        @endforeach
    </table>
    @if (count($data) > 0)
    <div class="row help">
		{{ __('poll.indexHelp') }}				
    </div>
    @else
    <div>{{ __('poll.notrecord') }}</div>
    @endif  
    {!! $data->links() !!}
    
  </div>        
</x-guest-layout>  
