<?php 
/**
 * create url from user record
 * @param unknown $user
 * @return string
 */
function avatar($profile_photo_path) {
    if ($profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

?>
<x-guest-layout>
      <!-- 
        params: members,parentType,parent,admin,
            order,orderDir,filterStr,
            parentPath
      -->
      
<div id="membersBrowser" class="pageContainer row membersBrowser">
            	<div class="row filters">
            		@if (count($parentPath) > 0)
            		    <div class="row parentPath">
				        	<ul>
				        	@foreach ($parentPath as $p)
				        		<li>
				        			<a href="{{ URL::to('/') }}/group/show/{{ $p->id }}"
				        				title="{{ __('members.show') }}">
				        				<em class="fa fa-eye"></em>&nbsp;
				        				{{ $p->name }}
				        			</a>
				        		</li>	
				        	@endforeach
				        	</ul>
				        </div>
				    @else
	        			<a href="{{ URL::to('/') }}/{{ $parentType }}/show/{{ $parent->id }}"
	        				title="{{ __('groups.show') }}">
	        				<em class="fa fa-eye"></em>&nbsp;
	        				{{ $parent->name }}
	        			</a>
            		@endif
            	</div>
            	<h2>{{ __('members.list') }}</h2>
            	<div class="row searchForm">
            		<form method="get" id="memberSearch" action="">
            			<input type="text" id="filterStr" name="filterStr" 
            				value="{{ $filterStr }}" />
            			<button class="btn btn-primary" type="submit"
            				title="{{ __('members.search') }}">
            				<em class="fa fa-search"></em>
            			</button>
            			<button class="btn btn-secondary" type="submit" 
            				onclick="$('#filterStr').val('');"
            				title="{{ __('members.clearSearch') }}">
            				<em class="fa fa-times"></em>
            			</button>
            			
            		</form>
            	</div>
				<table class="table table-bordered table-hover">
				    <thead>
				        <th class="name">
				        	<a href="?page=1&order=name">
				        	{{ __('members.name') }}
				        	@if ($order == 'name')
				        		@if ($orderDir == 'ASC')
				        			<em class="fa fa-caret-down"></em>
				        		@else
				        			<em class="fa fa-caret-up"></em>
				        		@endif
				        	@endif
				        	</a>
				        </th>
				        <th class="ranks">
				        	{{ __('members.ranks') }}
				        </th>
				    </thead>
				    <tbody>
				        @if ($members->count() == 0)
				        <tr>
				            <td colspan="2">{{ __('members.notrecords') }}</td>
				        </tr>
				        @endif
				
				        @foreach ($members as $member)
				        <tr>
				            <td>
				            	<a href="{{ url('/') }}/member/form/{{ $parentType }}/{{ $parentId }}/{{ $member->name }}"
				            	   title="{{ __('members.show') }}">&nbsp;
			        				<img class="avatar" src="{{ avatar($member->profile_photo_path) }}" />&nbsp;
			        				{{ $member->name }}
								</a>
				            </td>
				            <td>
				              {{ __('members.'.$member->ranks) }}
				              @if ($member->current_team_id === 0)
				              	&nbsp;{{ __('members.sysadmin') }}
				              @endif
				            </td>
				        </tr>
				        @endforeach
				    </tbody>
				</table>
				{{ $members->links() }}
            </div>
        </div>
    </div>
</div>  
</x-guest-layout>
