<?php 
/**
 * create url from user record
 * @param unknown $user
 * @return string
 */
function avatar() {
    $user = \Auth::user();
    if ($user->profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$user->profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>
<div id="messages" class="pageContainer row messagesBrowser">

	<h2>
		<a href="{{ \URL::to('/'.$parentType.'/'.$parent->id) }}">
  		<em class="fas fa-hand-point-right"></em>
		@if ($parentType == 'teams')
		<em class="fas fa-user-friends"></em>
		@endif
		@if ($parentType == 'projects')
		<em class="fas fa-cogs"></em>
		@endif
		@if ($parentType == 'polls')
		<em class="fas fa-retweet"></em>
		@endif
		&nbsp;{{ $parent->name }}
		</a>
	</h2>
	<p> {{ __('messages.'.$parentType) }}</p>
   	<h3>{{ __('messages.list') }}</h3>

	{!! $tree !!}
	
	@if ($member)
	<div class="row newMsg">
	<form method="post" action="{{ \URL::to('/message/store') }}"
		<input type="hidden" name="parent_type" value="{{ $parentType}}" />
		<input type="hidden" name="parent" value="{{ $parentId }}" />
		<input type="hidden" name="reply_to" value="0" />
		<input type="hidden" name="msg_type" value="" />
    	<textarea name="value" cols="60" rows="4" style="width:70%"></textarea>
    	<button type="submit" class="btn btn-primary">
    		<em class="fas fa-paper-plane"></em>{{ __('messages.send') }}
    	</button>
	</form>
	</div>
	<div class="row" class="chatBtn">
		<button type="button" class="btn  btn-secondary" onclick="jitsiStart()">
			<em class="fas fa-video"></em>&nbsp;<em class="fas fa-microphone"></em>&nbsp;Chat
		</button>
	</div>
	@endif
	
	<div class="help" id="jitsiHelp" style="display:none">
		    {!! __('messages.help') !!}
			<div>on-line:
				<a href="#" id="onlineCount"></a>
			</div>
	</div>
	<div id="onlineMembers" style="display:none; width:15%"; float:right">
			<h3>{{ __('messages.online') }}</h3>
			<div id="onlineList"></div>
	</div>		
	<div style="clear:both"></div>
	@if ($member)
		<div id="jitsi">
		<script type="text/javascript">
   		function jitsiStart() {
		    $('#jitsiHelp').show();
            var domain = "meet.jit.si";
            var w = window.innerWidth - 15;
            var options = {
                roomName: "{{ $parentType }}_{{ $parent->name }}",
                width: w,
                height: w,
                parentNode: undefined,
                configOverwrite: {},
                interfaceConfigOverwrite: {
                    filmStripOnly: true
                },
                userInfo: {
        				email: '{{ \Auth::user()->email }}',
        				displayName: '{{ \Auth::user()->name }}'
    			}
            }
            var avatar = "{{ avatar() }}";
            
            var api = new JitsiMeetExternalAPI(domain, options);
			var s = api.getParticipantsInfo();
			$('#onlineCount').html(s.length);
			api.addListener('videoConferenceJoined', function(p) {
					api.executeCommand('displayName', '{{ \Auth::user()->name }}');
					api.executeCommand('avatarUrl', avatar);
					var s = api.getParticipantsInfo();
					$('#onlineCount').html(s.length);
			});
			return false;
		}
			
		$('#onlineCount').click(function() {
			    var i = 0;
				var s = api.getParticipantsInfo();
				var div = $('#onlineList');
				$('#onlineCount').html(s.length);
				div.html('');
				for (i=0; i < s.length; i++) {
					div.append('<p>'+s[i].displayName+'</p>');
				}
 			    $('#messagesList').css('width','70%');
			    $('#onlineMembers').show();
			    return false;
		});

		function replyClick(id) {
			$('#reply'+id).show();
		}
        </script>
		</div>
		@endif
</div>
</x-guest-layout>
