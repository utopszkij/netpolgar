<?php 

if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>
<div id="messages" class="pageContainer row messagesTree">

	<h2>{{ $parent->name }}</h2>
	<p> {{ __('messages.'.$parentType) }}</p>
   	<h3>{{ __('messages.list') }}</h3>
	
	<div class="paths">
	@foreach ($path as $pathItem)
		<div>
			<a href="{{ \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$pathItem->id) }}">
				{{ $pathItem->name }}<br /> {{ $pathItem->value}}<br />
			</a>a>	
		</div>
	@endforeach
	</div>
	
	<div class="tree">
	@foreach ($tree as $treeItem) 
		@php if ($treeItem->level > 4) $treeItem->level = 4; @endphp
		<div class="msg level{{ $treeItem->level }}">
			@if ($treeItem->id > 0)
			<div class="msgHeader">
				<img class="avatar" src="{{ $treeItem->avatar }}" />&nbsp;
				{{ $treeItem->creator }}&nbsp;
				{{ $treeItem->time }}&nbsp;
				@if ($moderator)
					<a href="{{ \URL::to('/message/moderal/'.$treeItem->id) }}"><em class="fas fa-edit"></em></a>
				@endif
			</div>
			<div class="msgBody">
				@if ($treeItem->replyTo[1] != '')
				<div class="replyTo">{{ $treeItem->replyTo[1] }}</div>
				@endif	
			  	{!! str_replace("\n",'<br />',$treeItem->text) !!}
				@if ($treeItem->moderatorInfo != '')
				<div class="moderatorInfo">{{ $treeItem->moderatorInfo }}</div>
				@endif			  	
			</div>
			<div class="msgFooter">
				<a href="{{ \URL::to('/like/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					<em class="fas fa-thumbs-up"></em>
				</a>&nbsp;
				<a href="{{ \URL::to('/likeinfo/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					{{ $treeItem->likeCount }}</em>
				</a>&nbsp;
				<a href="{{ \URL::to('/dislike/messages/'.$treeItem->id) }}" class="{{ $treeItem->disLikeStyle }}">
					<em class="fas fa-thumbs-down"></em>
				</a>&nbsp;
				<a href="{{ \URL::to('/likeinfo/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					{{ $treeItem->disLikeCount }}</em>
				</a>&nbsp;
				@if ($member)
				<a href="#" onclick="replyClick({{ $treeItem->id }})">
					<em class="fas fa-reply"></em> Válasz
				</a>&nbsp;&nbsp;&nbsp;
				@endif
				<a href="{{ \URL::to('/message/protest/'.$treeItem->id) }}">
					<var class="protest"><em class="fas fa-ban"></em>Jelentem</var>
				</a>&nbsp;
			</div>
			<div id="reply{{ $treeItem->id }}" style="display: none">
            	<form method="post" action="{{ \URL::to('/message/store') }}">
				 	@csrf
            		<input type="hidden" name="parent_type" value="{{ $parentType }}" />
            		<input type="hidden" name="parent" value="{{ $parent->id }}" />
            		<input type="hidden" name="reply_to" value="{{ $treeItem->id }}" />
            		<input type="hidden" name="msg_type" value="" />
                	<textarea id="replyText{{ $treeItem->id }}" name="value" cols="60" rows="4" style="width:70%"></textarea>
                	<button type="submit" class="btn btn-primary">
                		<em class="fas fa-paper-plane"></em>{{ __('messages.send') }}
                	</button>
            	</form>
			</div>
			@else
			<div class="excluded">
    			<a href="{{ \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$treeItem->replyTo[0]) }}">
    				...
    			</a>
			</div>
			@endif
		</div>
	@endforeach
	</div>
	
	<div class="paginator">
	@foreach ($links as $link)
		@if ($link[0] == 'actual')
			<var class="actual">{!! $link[1] !!}</var>
		@else
			<a href="{{ $link[2] }}" class="{{ $link[0] }}">{!! $link[1] !!}</a>
		@endif
	@endforeach
	</div>
	
	@if ($member)
	<div class="row newMsg">
	<form method="post" action="{{ \URL::to('/message/store') }}">
	 	@csrf
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
            var avatar = "{{ $avatar }}";
            
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
			$('#replyText'+id).focus();
			return false;
		}
        </script>
		</div>
		@endif
</div>
</x-guest-layout>
