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

if ($parent) {
    $parent_id = $parent->id;
    $parent_name = $parent->name;
} else {
    $parent_id = 0;
    $parent_name = '';
}

function option($act, $value) {
    if ($act == $value) {
        echo '<option value="'.$value.'" selected="selected">'.__('groups.'.$value).'</option>'."\n";
    } else {
        echo '<option value="'.$value.'">'.__('groups.'.$value).'</option>'."\n";
    }
}
?>
<x-guest-layout>
      <!-- 
        params: group,  parent, parentPath, user, creator
      -->
<div id="groupShow" class="pageContainer row groupShow">
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
            			<a href="{{ URL::to('/') }}/groups/{{ $p->id }}/0/0"
            				title="{{ __('groups.open') }}">
            				<em class="fa fa-folder-open"></em>
            				{{ $p->name }}
            			</a>
            		</li>	
            	@endforeach
            	</ul>
            </div>
    	@endif
    </div>
    <div class="row">
	<h2 class="title">
   			{{ __('groups.details') }}
   	</h2>
  	<a href="#" onclick="$('#submenu').toggle()" class="submenuIcon" title="{{ _('submenu') }}">
   			<em class="fa fa-bars"></em>
   	</a>
   	<div class="submenu col-lg-3" id="submenu">
   		<ul>
   			<li>
   				<a href="{{ URL::to('/') }}/members/group/{{ $group->id }}">
   					<em class="fa fa-user"></em>&nbsp;{{ __('groups.members') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/admin/group/{{ $group->id }}">
   					<em class="fa fa-user-plus"></em>&nbsp;{{ __('groups.admins') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/groups/{{ $group->id }}/0/0">
   					<em class="fa fa-play"></em>&nbsp;{{ __('groups.subgroups') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/projects/{{ $group->id }}/0">
   					<em class="fa fa-cogs"></em>&nbsp;{{ __('groups.projects') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/products/{{ $group->id }}">
   					<em class="fa fa-cart-plus">&nbsp;</em>{{ __('groups.products') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/messages/group/{{ $group->id }}">
   					<em class="fa fa-comment">&nbsp;</em>{{ __('groups.messages') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/votes/group/{{ $group->id }}">
   					<em class="fa fa-check-square">&nbsp;</em>{{ __('groups.votes') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/events/group/{{ $group->id }}">
   					<em class="fa fa-calendar">&nbsp;</em>{{ __('groups.events') }}
   				</a>
   			</li>	
   			<li>
   				<a href="{{ URL::to('/') }}/fields/group/{{ $group->id }}">
   					<em class="fa fa-folder-open">&nbsp;</em>{{ __('groups.files') }}
   				</a>
   			</li>	
   			@if ($member->rank == 'admin')
   			<li>
   				<a href="{{ URL::to('/') }}/group/form/{{ $group->id }}/{{ $group->parent_id }}">
   					<em class="fa fa-edit">&nbsp;</em>{{ __('groups.edit') }}
   				</a>
   			</li>	
   			@endif
   		</ul>
   	</div>
    <div id="formShow" method="post" 
    	action="{{ URL::to('/') }}/group/save" class="form col-lg-9">
    	@csrf
    	<input type="hidden" name="id" value="{{ $group->id }}" />
    	<input type="hidden" name="parent_id" value="{{ $group->parent_id }}" />
    	<input type="hidden" name="created_at" value="{{ $group->created_at }}" />
    	<input type="hidden" name="created_by" value="{{ $group->created_by }}" />
    	<input type="hidden" name="updated_at" value="{{ $group->updated_at }}" />
    	<div class="form-group">
			<label>{{ __('groups.name') }}:</label>
			<input type="text" name="name" disabled="disabled" class="form-control" value="{{ $group->name }}" />    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.description') }}:</label>
			<textarea cols="80" rows="5" readonly="readonly" name="description" class="form-control">{{ $group->description }}</textarea>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.avatar') }}:</label>
			<input type="text" size="80" id="avatar" name="avatar" disabled="disabled" 
				class="form-control" value="{{ $group->avatar }}" onchange="avatarChange()" />
			<img id="imgAvatar" src="{{ $group->avatar }}" class="avatar" />    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.status') }}:</label>
			@if ($group->status == 'proposal')
				<var>{{ __('groups.'.$group->status) }}</var>
				<input type="hidden" name="status" disabled="disabled" value="{{ $group->status }}" />
			@else
				<select name="status" disabled="disabled">
					<?php 
					option($group->status,'proposal');
					option($group->status,'active');
					option($group->status,'closed');
					option($group->status,'paused');
					option($group->status,'deleted');
					?>
				</select> 
			@endif
		</div>  
    	<div class="form-group">
			<label>{{ __('groups.config') }}:</label>
			<textarea cols="80" rows="10" name="config" readonly="readonly" class="form-control">{{ $group->config }}</textarea>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.creator') }}:</label>
			<var>{{ $creator->name }}</var>    	
    	</div>
    	
    	<div class="buttons">
    		<a href="{{ URL::previous() }}" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;{{ __('groups.ok') }}
    		</a>
    	</div>
    	<div class="likes">
    	@if ($group->status == 'proposal')
    		@if ($userLiked)
    			<em class="fa fa-check"></em>&nbsp;
    		@endif 
    		<a href="{{ URL::to('/') }}/like/group/{{ $group->id }}/like">
	    		Aktiválását javaslom&nbsp;
    			<em class="fa fa-thumbs-up"></em>
    		</a>
    		&nbsp;
    		<var><a href="{{ URL::to('/') }}/likelist/group/{{ $group->id }}/like">{{ $likeCount }}</a></var>
    	@endif
    	@if ($group->status == 'active')
    		@if ($userDisLiked)
    			<em class="fa fa-check"></em>&nbsp;
    		@endif 
    		<a href="{{ URL::to('/') }}/like/group/{{ $group->id }}/dislike">
	    		Lezárását javaslom&nbsp;
    			<em class="fa fa-thumbs-down"></em>
    		</a>
    		&nbsp;
    		<var><a href="{{ URL::to('/') }}/likelist/group/{{ $group->id }}/dislike">{{ $disLikeCount }}</a></var>
    	@endif
    		
    	</div>
    </div>
</div>  
<script type="text/javascript">
function avatarChange() {
	$('#imgAvatar').attr('src',$('#avatar').val());
}
</script>
</x-guest-layout>
