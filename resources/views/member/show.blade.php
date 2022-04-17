<script src="https://meet.jit.si/external_api.js"></script>
<x-guest-layout>  

	<div id="memberContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2><a href="{{ \URL::to('/'.$member->parent_type.'/'.$member->parent) }}">
                	{{ $parent->name }}</a>
                </h2>
                <h3>{{ __('member.details') }}</h3>
            </div>
        </div>
    </div>
    
	<div class="row">
		<div class="col-1 col-md-2" id="memberMenu">
			<var id="subMenuIcon" class="subMenuIcon" onclick="toggleTeamMenu()">
				<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="{{ \URL::to('/member/list/'.$member->parent_type.'/'.$member->parent) }}">
            <em class="fas fa-reply"></em>
            <span>{{ __('member.back') }}</span><br />
         </a>
		 <a href="{{ \URL::to('/users/'.$member->user_id.'/teams') }}" title="{{ __('member.groups') }}">
				<em class="fas fa-sitemap"></em>
				<span>{{ __('member.groups') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/projectsbyuser/'.$member->user_id) }}" title="{{ __('member.projects') }}">
				<em class="fas fa-cogs"></em>
				<span>{{ __('member.projects') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/products/listbyuser/'.$member->user_id) }}" title="{{ __('member.products') }}">
				<em class="fas fa-shopping-basket"></em>
				<span>{{ __('member.products') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/construction') }}" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span>{{ __('member.files') }}</span><br />			
		 </a>
		</div>
        
        @php 
        	$user->profile_photo_path = \App\Models\Avatar::userAvatar($user->profile_photo_path, $user->email);
			if (\Auth::user()) {
        		$loggedAvatar = \App\Models\Avatar::userAvatar(\Auth::user()->profile_photo_path, 
				                                               \Auth::user()->email);
			}	
        @endphp
        
	    <div class="col-11 col-md-10">
	    	<h4>{{ $user->name }}</h4>
	    	<p>{{ implode(',',$ranks) }}</p>
	    	<p><img src="{{ $user->profile_photo_path }}" style="width:30%" /></p>
	    </div>
	    
	  </div> <!-- .row -->
	  @if (\Auth::check())

	  <div class="row">
	  <h4>{{ __('member.msg') }}</h4>
        	<form method="post" action="{{ \URL::to('/message/store') }}">
			 	@csrf
        		<input type="hidden" name="parent_type" value="users" />
       			<input type="hidden" name="parent" value="{{ $user->id }}" />
        		<input type="hidden" name="reply_to" value="0" />
        		<input type="hidden" name="msg_type" value="" />
            	<textarea id="value" name="value" cols="60" rows="4" style="width:70%"></textarea>
            	<button type="submit" class="btn btn-primary">
            		<em class="fas fa-paper-plane"></em>{{ __('member.send') }}
            	</button>
        	</form>
        	<p>használható korlátozott "markdown" szintaxis.
    			kiemelt: <strong>**...**</strong>,
    			dölt betüs: <strong>*...*</strong> ,
    			kép: <strong>![](http...)</strong>, 
    			link: <strong>http....</strong>
    		</p>
	  </div>
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
	@endif

   <script>
		function toggleTeamMenu() {
			var teamMenu = document.getElementById('teamMenu');
			if (teamMenu.style.width == "100%") {
				teamMenu.style.width="8.3%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="none";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-right"></em>';
			} else {
				teamMenu.style.width="100%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="inline-block";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-left"></em>';
			}
			return false;	
		}  
		@if (\Auth::check()) 
		function jitsiStart() {
		    $('#jitsiHelp').show();
            var domain = "meet.jit.si";
            var w = window.innerWidth - 15;
            var options = {
                roomName: "users_{{ $user->name }}",
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
		@endif
   </script> 
    
   
   </div>
</x-guest-layout>  
