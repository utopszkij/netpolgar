<?php
/**
 * create url from user photo_path
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

/**
 * member rekord status modositása a likeCount/dislikeCount alapján
 * @param unknown $member
 * 1. $member->parent_type és ->parent_id alapján parent rekord olvasása
 * 2. parent memberCount olvasása
 * 3. $member->status, $parent->config, $memberCount és $likeCount, $dsiLikeCouunt
 *    alapján szükség szerint státus modositás (az adatbázisban is)
 */
function statusAdjust(& $member, int $likeCount, $disLikeVount) {
    
}


/**
 * echo select option
 * @param unknown $act
 * @param unknown $value
 */
function option(string $act, string $value) {
    if ($act == $value) {
        echo '<option value="'.$value.'" selected="selected">'.__('members.'.$value).'</option>'."\n";
    } else {
        echo '<option value="'.$value.'">'.__('members.'.$value).'</option>'."\n";
    }
}
?>
<x-guest-layout>
      <!-- 
        params: members,  parent, parentType, admin
      -->
<div id="memberForm" class="pageContainer memberForm">
	<h4>{{ $parent->name }}</h4>
	<p>{{ __('members.'.$parentType )}} {{ __('members.member') }}</p>
	<p><img src="{{ avatar($members[0]->profile_photo_path) }}" class="avatar" />&nbsp;
    	{{ $members[0]->name}}&nbsp;
    </p>
	@if (count($members) > 0)
	    <form id="formMember" method="post" 
    	action="{{ URL::to('/') }}/member/save" class="form col-lg-12">
    	@csrf
    	<input type="hidden" name="parentType" value="{{ $parentType }}" />
    	<input type="hidden" name="parentId" value="{{ $parent->id }}" />
    	<input type="hidden" name="name" value="{{ $members[0]->name }}" />
    	@foreach ($members as $member)
    		  <?php 
    	      // like,dislike információk
              $messageModel = new \App\Models\Messages();
              $likeCount = $messageModel->where('parent_type','=',$parentType.'member')
              ->where('parent_id','=',$member->id)
              ->where('type','=','like')
              ->count();
              $messageModel = new \App\Models\Messages();
              $disLikeCount = $messageModel->where('parent_type','=',$parentType.'member')
              ->where('parent_id','=',$member->id)
              ->where('type','=','dislike')
              ->count();
              $user = \Auth::user();
              if ($user) {
                  $messageModel = new \App\Models\Messages();
                  $userLiked = ($messageModel->where('parent_type','=',$parentType.'member')
                      ->where('parent_id','=',$member->id)
                      ->where('type','=','like')
                      ->where('user_id','=',$user->id)
                      ->count() > 0);
                  $messageModel = new \App\Models\Messages();
                  $userDisLiked = ($messageModel->where('parent_type','=',$parentType.'member')
                      ->where('parent_id','=',$member->id)
                      ->where('type','=','dislike')
                      ->where('user_id','=',$user->id)
                      ->count() > 0);
              } else {
                  $userLiked = 0;
                  $userDisLiked = 0;
                  $admin = false;
              }
              statusAdjust($member, $likeCount, $disLikeCount);
              ?>
    	
    		<div class="form-group">
    			@if (\Auth::user())
    		   		@if (($admin) | (\Auth::user()->current_team_id == 0))
        				<select name="status_{{ $member->id }}" class="form-control">
        				{{ option($member->status,'proposal') }}
        				{{ option($member->status,'active') }}
        				{{ option($member->status,'closed') }}
        				{{ option($member->status,'excluded') }}
        				{{ option($member->status,'deleted') }}
        				</select>&nbsp;
        			@else
        				{{ __('members.'.$member->status) }}&nbsp;
        			@endif
        		@else
       				{{ __('members.'.$member->status) }}&nbsp;
    			@endif
    			{{ __('members.'.$member->rank) }}&nbsp;
    			@if ($member->status == 'proposal')
    			    &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;{{ __('members.like') }}&nbsp;
    			    @if ($userLiked)
    			    	<em class="fa fa-check"></em> 
    			    @endif
    				<a href="{{ URL::to('/') }}/like/{{ $parentType }}member/{{ $member->id }}/like">
    					<em class="fa fa-thumbs-up"></em>&nbsp;
    				</a>
    				({{ $likeCount }})
    			@endif
    			@if ($member->status == 'active')
    			    &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;{{ __('members.dislike') }}&nbsp;
    			    @if ($userDisLiked)
    			    	<em class="fa fa-check"></em> 
    			    @endif
    				<a href="{{ URL::to('/') }}/like/{{ $parentType }}member/{{ $member->id }}/dislike">
    					<em class="fa fa-thumbs-down"></em>&nbsp;
    				</a>
    				({{ $disLikeCount }})
    			@endif
    		</div>
    	@endforeach
    	@if (\Auth::user())
    		@if (($admin) | (\Auth::user()->current_team_id == 0))
    			<div class="form-group add">
    			    <br />
        			{{ __('members.add') }}
        			<select name="rank" class="form-control">
        				<option value="">&nbsp;</option>
        				<option value="member">{{ __('members.member') }}</option>
        				<option value="admin">{{ __('members.admin') }}</option>
        			</select>
        		</div>
    			<div class="form-buttons">
    				<button type="submit" class="btn btn-primary">
    					<em class="fa fa-check"></em>&nbsp;{{ __('Save') }}
    				</button>&nbsp;
            		<a href="{{ URL::previous() }}" class="btn btn-secondary">
            			<em class="fa fa-reply"></em>&nbsp;{{ __('Cancel') }}
            		</a>
    			</div> 
    		@endif
    	@endif
    	</form>
	@endif	
</div>  
</x-guest-layout>
