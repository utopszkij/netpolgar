<x-guest-layout>  
	<div id="projectContainer">
  <div class="helpBtn">
			<a href="#" onclick="help('projects')">
				<em class="fas fa-book"></em>Súgó
			</a>	
		</div>
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('project.projects') }}</h2>
            </div>
        </div>
    </div>
    <div class="row">
    	<h3>
			@if ($team)
			<a href="{{ \URL::to('/teams/'.$team->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-user-friends"></em>
				&nbsp;{{ $team->name }} 			
			</a>
			@endif
			@if ($user)
			<a href="{{ \URL::to('/member/user/'.$user->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-user"></em>
				&nbsp;{{ $user->name }} 			
			</a>
			@endif
			
		</h3>	    	
	 </div>    

	@if ($team)
		@if (($team->status == 'active') &
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
    @endif
     
    <table class="table table-bordered indexTable">
    	<thead>
        <tr>
            <th>{{ __('project.status') }}</th>
            <th>{{ __('project.deadline') }}</th>
            <th>{{ __('project.name') }}</th>
            <th>{{ __('project.description') }}</th>
        </tr>
      </thead>
      <tbody>  
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
      </tbody>  
    </table>
    @if (count($data) > 0)
    <div class="row help">
    	<div class="col-12">
			Részletekért és további lehetőségért kattints a project nevére!
		</div>				
    </div>
    
    {!! $data->links('pagination') !!}
    @else
    <div>{{ __('project.notrecord') }}</div>
    @endif  
    
  </div>        
</x-guest-layout>  
