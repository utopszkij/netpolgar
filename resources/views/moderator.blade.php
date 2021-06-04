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

?>
<x-guest-layout>
      <!-- 
        params: message,  parent, sender
      -->
<div id="moderatorForm" class="pageContainer row moderatorForm">
    <h2>{{ __('messages.moderatorForm') }}</h2>
    <form id="formModerator" method="post" action="{{ URL::to('/') }}/message/savemoderation" class="form">
    	@csrf
    	<input type="hidden" name="id" value="{{ $message->id }}" />
    	<div class="form-group">
			<label>{{ __('messages.target') }}:</label>
			<var>{{ $parent->name }}</var> {{ __('messages.'.$message->parent_type) }}   	
    	</div>
    	<div class="form-group">
			<label>{{ __('messages.sender') }}:</label>
			<var>{{ $sender->name }}</var>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('messages.created_at') }}:</label>
			<var>{{ $message->created_at }}</var>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('messages.value') }}:</label>
			<textarea cols="80" rows="10" name="value" class="form-control">{{ $message->value }}</textarea>    	
    	</div>
    	<div class="form-group">
			<label>{{ __('messages.moderatorinfo') }}:</label>
			<textarea cols="80" rows="10" name="moderatorinfo" class="form-control">{{ $message->moderatorinfo }}</textarea>    	
    	</div>
    	<div class="buttons">
    		<button type="submit" class="btn btn-primary">
    			<em class="fa fa-check"></em>&nbsp;{{ __('messages.save') }}
    		</button>&nbsp;
    		<a href="{{ URL::previous() }}" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;{{ __('messages.cancel') }}
    		</a>
    	</div>
    </form>
</div>  
</x-guest-layout>
