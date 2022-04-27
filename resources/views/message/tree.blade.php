<?php 

if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}

// include_once \Config::get('view.paths')[0].'/minimarkdown.php';
use App\Models\Minimarkdown;
if (\Auth::user()) {
	$loggedAvatar = \App\Models\Avatar::userAvatar(\Auth::user()->profile_photo_path, 
												   \Auth::user()->email);
}	

?>
<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>
<div id="messages" class="pageContainer row messagesTree">
	<p>&nbsp;</p>
	<div class="helpBtn">
		<a href="{{ \URL::to('/help').'?name=beszelgetes' }}" target="_help" onclick="true">  
		<!-- a href="#" onclick="help('messages')"> -->
			<em class="fas fa-book"></em>Súgó
		</a>	
	</div>
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
		&nbsp;
	</h2>
	<p> {{ __('messages.'.$parentType) }}</p>
	@if (\Auth::check())
		<div class="row">
			<div class="col-12">
				<a href="{{ \URL::to('/message/notreaded') }}">
					<em class="fas fa-hand-point-right"></em>{{ __('messages.notreaded') }}
				</a>
			</div>
		</div>
	@endif
	@if ($title != __('messages.tree'))
		<div class="col-12">
				<a href="{{ \URL::to('/message/tree/'.$parentType.'/'.$parent->id) }}">
					<em class="fas fa-hand-point-right"></em>{{ __('messages.tree') }}
				</a>
		</div>
	@endif

	<h4>{{ $title }}</h4>   
	<div id="forum">
		<form method="get" action="{{ \URL::to('/message/list/'.$parentType.'/'.$parent->id.'/0') }}">
			<input type="hidden" name="page" value="0" />
			<input type="hidden" name="offset" value="0" />
			Szürés, csak <input type="text" name="filterUserName" value="{{ $filterUserName }}"/> üzenetei
			<button type="submit" class="btn btn-primary">
				<em class="fas fa-search"></em>
				szűrés</button>
		</form>	
		
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
			<a href="{{ \URL::current() }}" class="btn btn-secondary" style="color:white">
					<em class="fas fa-ban"></em>{{ __('messages.cancel') }}
           	</a>
			<p>használható korlátozott "markdown" szintaxis.
				kiemelt: <strong>**...**</strong>,
				dölt betüs: <strong>*...*</strong> ,
				kép: <strong>![](http...)</strong>, 
				link: <strong>http....</strong>
				:(,   :),  :|<br />
				max. 3 kép lehet, max. képfile méret: 2M
			</p>
		</form>
		</div>
		@endif
	</div>

	<!-- jitsi start -->

	<div class="row" class="chatBtn" style="border-style:solid; margin:5px; padding:10px;">
		<h4>Video chat</h4>
		<button type="button" class="btn  btn-secondary" onclick="jitsiStart()">
			<em class="fas fa-video"></em>&nbsp;<em class="fas fa-microphone"></em>&nbsp;Chat
		</button>
		A web böngésző biztonsági beállításai, egyes esetekben megadályozhatják ennek a funciónak a müködését.
		Arra van szükség, hogy a web böngésző használhassa a mikrofont és a kamerát.
	</div>
	
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
	
<!-- jitsi end -->

	<div style="clear:both"></div>
	@if (\Auth::check())
	@if (($member) | 
		 (($parentType == 'users') & ($parentId == \Auth::user()->id))
		)
		<div id="jitsi" style="height:600px"></div>
		<script type="text/javascript">
   		function jitsiStart() {
			$('#forum').hide();   
		    $('#jitsiHelp').show();
            var domain = "meet.jit.si";
            var w = window.innerWidth * 0.9;
            var h = window.innerHeight * 0.9;
            var options = {
                roomName: "{{ $parentType }}_{{ $parent->name }}",
                width: w,
                height: h,
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
            var avatar = "{{ $loggedAvatar }}";
            
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

		var scrollTop = 0;
		function replyClick(id) {
			var top  = window.pageYOffset || document.documentElement.scrollTop;
			scrollTop = top;
			$('#reply'+id).show();
			$('#jitsyButton').show();
			$('#replyText'+id).focus();
			if (top > 50) {
				top = top - 50;
			}
			setTimeout(myScrollTo,100);
			return false;
		}
		function myScrollTo() {
			window.scrollTo(0,scrollTop);
		}
        </script>
		</div>
	@endif
	@endif
</div>
</x-guest-layout>
