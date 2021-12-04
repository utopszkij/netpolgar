<?php 
/**
 * create url from user record
 * @param unknown $user
 * @return string
 */
function avatar($user) {
    if ($user->profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$user->profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

if ($member) {
    $member_id = $member->id;
} else {
    $member_id = 0;
}
if ($admin) {
    $admin_id = $admin->id;
} else {
    $admin_id = 0;
}
if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<x-guest-layout>
      <!-- 
        params: groups, member, admin, order, orderDir, filerStr, 
                parent, parentPath
                member és admin lehet false is!
                parentPath array of group_id
      -->
<div id="groupsBrowser" class="pageContainer row groupsBrowser">
            	<div class="row filters">
            		@if (count($parentPath) > 0)
            		    <div class="row parentPath">
            		    	<h4>{{ __('groups.parents') }}</h4>
				        	<ul>
				        	@foreach ($parentPath as $p)
				        		<li>
				        			<a href="{{ URL::to('/') }}/group/show/{{ $p->id }}"
				        				title="{{ __('groups.show') }}">
				        				<em class="fa fa-eye"></em>
				        			</a>
				        			&nbsp;
				        			<a href="{{ URL::to('/') }}/groups/{{ $p->id }}/{{ $member_id}}/{{ $admin_id}}"
				        				title="{{ __('groups.open') }}">
				        				<em class="fa fa-folder-open"></em>
				        				{{ $p->name }}
				        			</a>
				        		</li>	
				        	@endforeach
				        	</ul>
				        </div>
            		@endif
            		@if ($member)
            		<div class="row"><label>{{ __('groups.isMember') }}</label>
            			<a href="{{ URL::to('/') }}/member/show/{{ $member->id }}"> 
            				<img src="{{ avatar($member) }}" class="avatar" />
            				{{ $member->name }}
            			</a>	
            		</div>
            		@endif
            		@if ($admin)
            		<div class="row"><label>{{ __('groups.isAdmin') }}</label> 
            			<a href="{{ URL::to('/') }}/member/show/{{ $admin->id }}"> 
		            		<img src="{{ avatar($admin) }}" class="avatar" />
        		    		{{ $admin->name }}
        		    	</a>	
            		</div>
            		@endif
            	</div>
            	<h2>{{ __('groups.list') }}</h2>
            	<div class="row searchForm">
            		<form method="get" id="groupSearch" action="">
            			<input type="text" id="filterStr" name="filterStr" 
            				value="{{ $filterStr }}" />
            			<button class="btn btn-primary" type="submit"
            				title="{{ __('groups.search') }}">
            				<em class="fa fa-search"></em>
            			</button>
            			<button class="btn btn-secondary" type="submit" 
            				onclick="$('#filterStr').val('');"
            				title="{{ __('groups.clearSearch') }}">
            				<em class="fa fa-times"></em>
            			</button>
            			
            		</form>
            	</div>
				<table class="table table-bordered table-hover">
				    <thead>
				        <th class="id">
				        	<a href="?page=1&order=groups.id">
				        	{{ __('groups.id') }}
				        	@if ($order == 'groups.id')
				        		@if ($orderDir == 'ASC')
				        			<em class="fa fa-caret-down"></em>
				        		@else
				        			<em class="fa fa-caret-up"></em>
				        		@endif
				        	@endif
				        	</a>
				        </th>
				        <th class="name">
				        	<a href="?page=1&order=groups.name">
				        	{{ __('groups.name') }}
				        	@if ($order == 'groups.name')
				        		@if ($orderDir == 'ASC')
				        			<em class="fa fa-caret-down"></em>
				        		@else
				        			<em class="fa fa-caret-up"></em>
				        		@endif
				        	@endif
				        	</a>
				        </th>
				        <th class="status">
				        	<a href="?page=1&orde=groups.status">
				        	{{ __('groups.status') }}
				        	@if ($order == 'groups.status')
				        		@if ($orderDir == 'ASC')
				        			<em class="fa fa-caret-down"></em>
				        		@else
				        			<em class="fa fa-caret-up"></em>
				        		@endif
				        	@endif
				        	</a>
				        </th>
				    </thead>
				    <tbody>
				        @if ($groups->count() == 0)
				        <tr>
				            <td colspan="4">{{ __('groups.notrecords') }}</td>
				        </tr>
				        @endif
				
				        @foreach ($groups as $group)
				        <tr>
				        	<td>{{ $group->id }}</td>
				            <td>
				            	<a href="{{ url('/') }}/groups/{{ $group->id }}/0/0"
				            	   title="{{ __('groups.open') }}">
			        				<em class="fa fa-folder-open"></em>
								</a>
								&nbsp;			            	
				            	<a href="{{ url('/') }}/group/show/{{ $group->id }}"
				            		title="{{ __('groups.show') }}">
		        					<em class="fa fa-eye"></em>
									&nbsp;
				            		<img src="{{ $group->avatar }}" class="avatar" />
				            		{{ $group->name }}
				            	</a>
				            </td>
				            <td>{{ $group->status }}</td>
				        </tr>
				        @endforeach
				    </tbody>
				</table>
				{{ $groups->links() }}
				<div class="buttons">
					@auth
					<a href="{{ URL::to('/') }}/group/form/0/{{ $parent_id }}" class="btn btn-primary">
						<em class="fa fa-plus"></em>
						{{ __('groups.add') }}
					</a>
					@endauth
				</div>
            </div>
        </div>
    </div>
</div>  
</x-guest-layout>
