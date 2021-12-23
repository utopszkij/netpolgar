<x-guest-layout>  
	<div id="projectContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('project.projects') }}</h2>
            </div>
        </div>
    </div>
    <div class="row">
    	<h3>
			<a href="{{ \URL::to('/teams/'.$team->id) }}">
				<em class="fas fa-hand-point-right"></em>
				&nbsp;{{ $team->name }} 			
			</a>
		</h3>	    	
	 </div>    

    @if (($info->status == 'active') & 
         (count($info->userParentRank) > 0) &
         (!$info->parentClosed))
    <div class="row buttons">
       <a class="btn btn-primary" 
         href="{{ \URL::to('/'.$team->id.'/projects/create') }}">
        	<em class="fas fa-plus"></em>
        	{{ __('project.add') }}
       </a>
    </div>
    @endif
     
    <table class="table table-bordered">
        <tr>
            <th>{{ __('project.status') }}</th>
            <th>{{ __('project.deadline') }}</th>
            <th>{{ __('project.name') }}</th>
            <th>{{ __('project.description') }}</th>
        </tr>
        @foreach ($data as $key => $value)
        @php if ($value->avatar == '') $value->avatar = URL::to('/').'/img/team.png'; @endphp
        <tr>
            <td>{{ __('project.'.$value->status) }}</td>
            <td>{{ $value->deadline }}</td>
            <td>
            	<a href="{{ \URL::to('/projects/'.$value->id) }}">
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
		Részletekért és további lehetőségért kattints a project nevére!				
    </div>
    @else
    <div>{{ __('project.notrecord') }}</div>
    @endif  
    {!! $data->links('pagination') !!}
  </div>        
</x-guest-layout>  
