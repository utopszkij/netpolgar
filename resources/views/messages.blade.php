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

if ($member) {
    $member_id = $member->id;
} else {
    $member_id = 0;
}
if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>
      <!-- 
        params: items, parentType, parent, parentId 
                parentPath
                member, total, offset, filterStr
      -->
<div id="messagesBrowser" class="pageContainer row messagesBrowser">
		<h2>{{ $parent->name }}</h2>
		<p> {{ __('messages.'.$parentType) }}</p>
    	<h3>{{ __('messages.list') }}</h3>
    	<div class="row searchForm">
    		<form method="get" id="messagesSearch" action="">
    			<input type="text" id="filterStr" name="filterStr" 
    				value="{{ $filterStr }}" />
    			<button class="btn btn-primary" type="submit"
    				title="{{ __('messages.search') }}">
    				<em class="fa fa-search"></em>
    			</button>
    			<button class="btn btn-secondary" type="submit" 
    				onclick="$('#filterStr').val('');"
    				title="{{ __('messages.clearSearch') }}">
    				<em class="fa fa-times"></em>
    			</button>
    			
    		</form>
    	</div>
    	<div>
    	{{ __('messages.online') }}: <var id="onlineCount" style="cursor:pointer">0</var>
    	</div>
    	<div id="messagesList" style="display:inline-block; width:100%; float:left">
				    <div class="messages">
				        <!--  @ i f ($items->count() == 0) -->
				        @if (count($items) == 0)
				            {{ __('messages.notrecords') }}
				        @endif
				
				        @foreach ($items as $item)
				        	<div class="messageItem">
				        	    <span class="sender">
				        	    	<img src="{{ URL::to('/') }}/storage/app/public/{{ $item->profile_photo_path }}" class="avatar" />
				        	    	&nbsp;{{ $item->name }}
				        	    </span>
				        		<strong>&nbsp;#{{ $item->id }}</strong> 
				        		<span class="sendTime">&nbsp;{{ $item->created_at }}</span>
				        		@if ($member)
				        			@if (($member->rank == 'admin') | (\Auth::user()->current_tea_id == 0))
				        	    	<br />
				        	    	<pre>{!! $item->value !!}&nbsp;<a href="{{ URL::to('/') }}/messages/form/{{ $item->id }}"><em class="fa fa-edit"></em></a></pre>
				        	    	@else
					        	    	<br /><pre>{!! $item->value !!}</pre>
				        			@endif 
				        		@else
				        	    	<br /><pre>{!! $item->value !!}</pre>
				        	    @endif
				        	</div>
				        @endforeach
				    </div>
				<!--  { { $ items->links() } } -->
		</div>
		<div id="onlineMembers" style="display:none; width:15%"; float:right">
			<h3>{{ __('messages.online') }}</h3>
			<div id="onlineList">
			</div>
		</div>		
		<div style="clear:both"></div>
		@if ($member)
		<div class="help">
		    {!! __('messages.help') !!}
			
		</div>
		<div style="display:none">
			<iframe id="frmHidde"></iframe>
		</div>
		<div id="jitsi">
		<script type="text/javascript">
            var domain = "meet.jit.si";
            var options = {
                roomName: "{{ $parentType }}_{{ $parent->name }}",
                width: 700,
                height: 700,
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
			api.addListener('outgoingMessage', function(p) {
				if (p.message != '') {
					$('#frmHidde').attr('src',"{{ URL::to('/') }}/messageadd/{{ $parentType }}/{{ $parent->id}}/"
					+encodeURI(p.message));
				}	
			});
			api.addListener('videoConferenceJoined', function(p) {
					api.executeCommand('displayName', '{{ \Auth::user()->name }}');
					api.executeCommand('avatarUrl', avatar);
					var s = api.getParticipantsInfo();
					$('#onlineCount').html(s.length);
			});
			
			$('#onlineCount').click(function() {
			    var i = 0;
				var s = api.getParticipantsInfo();
				var div = $('#onlineList');
				$('#onlineCount').html(s.length);
				div.html('');
				for (i=0; i < s.length; i++) {
					div.append('ä href="{{ URL::to('/') }}/user/show/'+s[i].displayName+'">'+
						'<img src="'+s[i].avatarURL+'" class="avatar" />'+
						s[i].displayName+'</a><br />');
				}
 			    $('#messagesList').css('width','70%');
			    $('#onlineMembers').show();
			});
        </script>
		
		</div>
		@endif
</div>
</x-guest-layout>
