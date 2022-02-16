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
<div id="groupForm" class="pageContainer row groupForm">
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
    @if ($group->id == 0) 
    	<h2>{{ __('groups.add') }}</h2>
    @else
    	<h2>{{ __('groups.edit') }}</h2>
    @endif	
    <form id="formGroup" method="post" action="{{ URL::to('/') }}/group/save" class="form">
    	@csrf
    	<input type="hidden" name="id" value="{{ $group->id }}" />
    	<input type="hidden" name="parent_id" value="{{ $group->parent_id }}" />
    	<input type="hidden" name="created_at" value="{{ $group->created_at }}" />
    	<input type="hidden" name="created_by" value="{{ $group->created_by }}" />
    	<input type="hidden" name="updated_at" value="{{ $group->updated_at }}" />
    	<input type="hidden" name="activated_at" value="{{ $group->activated_at }}" />
    	<input type="hidden" name="closed_at" value="{{ $group->closed_at }}" />
    	<div class="form-group">
			<label>{{ __('groups.name') }}:</label>
			<input type="text" name="name" class="form-control" value="{{ $group->name }}" />    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.description') }}:</label>
			<textarea cols="80" rows="5" name="description" class="form-control">{{ $group->description }}</textarea>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.avatar') }}:</label>
			<input type="text" size="80" id="avatar" name="avatar" 
				class="form-control" value="{{ $group->avatar }}" onchange="avatarChange()" />
			<img id="imgAvatar" src="{{ $group->avatar }}" class="avatar" />    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.status') }}:</label>
			@if ($group->status == 'proposal')
				<var>{{ __('groups.'.$group->status) }}</var>
				<input type="hidden" name="status" value="{{ $group->status }}" />
			@else
				<select name="status">
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
			<textarea cols="80" rows="10" name="config" class="form-control">{{ $group->config }}</textarea>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('groups.creator') }}:</label>
			<var>{{ $creator->name }}</var>    	
    	</div>
    	
		  	
    	
    	<div class="form-buttons">
    		<button type="submit" class="btn btn-primary">
    			<em class="fa fa-check"></em>&nbsp;{{ __('groups.save') }}
    		</button>&nbsp;
    		<a href="{{ URL::previous() }}" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;{{ __('groups.cancel') }}
    		</a>
    	</div>
    </form>
</div>  
<script type="text/javascript">
function avatarChange() {
	$('#imgAvatar').attr('src',$('#avatar').val());
}
</script>
</x-guest-layout>
