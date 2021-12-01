<x-guest-layout>  
	<div id="teamContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('team.teams') }}</h2>
            </div>
        </div>
    </div>
    <div class="row path" style="margin-top: 5px;">
    @php $pathSeparator = ''; @endphp
    @foreach ($info->path as $item)
    	<var class="pathItem">
			<a href="{{ route('teams.show',["team" => $item->id]) }}">
				&nbsp;{!! $pathSeparator !!}&nbsp;{{ $item->name }} 			
			</a>    	
    	</var>
	    @php $pathSeparator = '<em class="fas fa-caret-right"></em>'; @endphp
	 @endforeach	    
	 </div>    

    @if (($info->status == 'active') & 
         (count($info->userRank) > 0) &
         (!$info->parentClosed))
    <div class="row buttons">
       <a class="btn btn-primary" 
         href="{{ route('parents.teams.create',["parent" => $parent]) }}">
        	<em class="fas fa-plus"></em>
        	{{ __('team.add') }}
       </a>
    </div>
    @endif
     
    <table class="table table-bordered">
        <tr>
            <th>{{ __('team.status') }}</th>
            <th>{{ __('team.name') }}</th>
            <th>{{ __('team.description') }}</th>
        </tr>
        @foreach ($data as $key => $value)
        @php if ($value->avatar == '') $value->avatar = URL::to('/').'/img/team.png'; @endphp
        <tr>
            <td>{{ __('team.'.$value->status) }}</td>
            <td>
            	<a href="{{ route('teams.show', $value->id) }}">
            	<img src="{{ $value->avatar }}" class="logo" alt="logo" title="logo" />
            	{{ $value->name }}
            	</a>
            </td>
            <td>{{ \Str::limit($value->description, 100) }}</td>
        </tr>
        @endforeach
    </table>
    @if (count($data) > 0)
    <div class="row help">
		Részletekért és további lehetőségért kattints a csoport nevére!				
    </div>
    @else
    <div>{{ __('team.notrecord') }}</div>
    @endif  
    {!! $data->links() !!}
    
  </div>        
</x-guest-layout>  
