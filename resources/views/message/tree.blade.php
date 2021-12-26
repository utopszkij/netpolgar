<?php 

if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}

/**
 * mini markdown parser
 * értelmezve: 
 *      http......     link
 *      ![](http...)   kép url
 *      **...**        kiemelt szöveg 
 * @param markdown string $s
 * @return html string
 */
function miniMarkdown($s) {
    $s = str_replace("\n",'<br />',$s);         // \n --> <br />
    $s = str_replace('![](http', 'img(', $s);   // kép url -t ne konvertálja <a -ra
    
    // link to <a href=...">...</a>
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    $s = preg_replace($url, '<a href="$0">$0</a>', $s);
    
    // **...** -> <strong>...</strong>
    $bold = '~\*\*([^\*<]+)\*\*~i';
    $s = preg_replace($bold, '<strong>$0</strong>', $s);
    $s = str_replace('**','',$s);
    
    // *...* -> <em>...</em>
    $em = '~\*([^\*<]+)\*~i';
    $s = preg_replace($em, '<em>$0</em>', $s);
    $s = str_replace('<em>*','<em>',$s);
    $s = str_replace('*</em>*','</em>',$s);
    
    // img(....) -> img src="..." />
    $img = '~img\(([^\s<]+)\)~i';
    $s = preg_replace($img, '<img src="$0)" />', $s);
    $s = str_replace('img(','http',$s);
    $s = str_replace('))','',$s);

    $s = str_replace(':)','<em class="fas fa-grin"></em>',$s);
    $s = str_replace(':(','<em class="fas fa-frown"></em>',$s);
    $s = str_replace(':|','<em class="fas fa-flushed"></em>',$s);
    return $s;
}

?>
<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>
<div id="messages" class="pageContainer row messagesTree">
	<p>&nbsp;</p>
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
   	<h3>
   		{{ __('messages.list') }}
   	</h3>
	
	<div class="paths">
	@foreach ($path as $pathItem)
    		@include('message.item', [
    			'treeItem' => $pathItem,
    			'parentType' => $parentType,
    			'parent' => $parent,
    			'parentId' => $parent->id 
    		])
	@endforeach
	</div>
	
	<div>
	@foreach ($tree as $treeItem) 
		@php if ($treeItem->level > 4) $treeItem->level = 4; @endphp
		@if ($treeItem->id > 0)
    		@include('message.item', [
    			'treeItem' => $treeItem,
    			'parentType' => $parentType,
    			'parent' => $parent,
    			'parentId' => $parent->id 
    		])
		@else
			<div class="excluded">
    			<a href="{{ \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/'.$treeItem->replyTo[0]) }}">
    				...
    			</a>
			</div>
		@endif
	@endforeach
	</div>
	
	<nav>
	<ul class="pagination pull-right">
	@foreach ($links as $link)
		@if ($link[0] == 'actual')
		<li class="page-item active" title="{{ $link[1] }}">
		<span class="page-link">{!! $link[1] !!}</span>
		</li>
		@else
		<li class="page-item" title="{{ $link[1] }}">
		<a href="{{ $link[2] }}" class="page-link">{!! $link[1] !!}</a>
		</li>
		@endif
	@endforeach
	</ul>
	</nav>
	
	@if (($member) & ($parentType != 'users'))
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
    	<p>használható korlátozott "markdown" szintaxis.
    		kiemelt: <strong>**...**</strong>,
    		dölt betüs: <strong>*...*</strong> ,
    		kép: <strong>![](http...)</strong>, 
    		link: <strong>http....</strong>
    		:(,   :),  :|
    	</p>
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
