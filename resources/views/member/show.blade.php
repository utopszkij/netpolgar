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
		 <a href="{{ \URL::to('/construction') }}" title="csoportok">
				<em class="fas fa-sitemap"></em>
				<span>{{ __('member.groups') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/construction') }}" title="Projektek">
				<em class="fas fa-cogs"></em>
				<span>{{ __('member.projects') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/construction') }}" title="Termékek">
				<em class="fas fa-shopping-basket"></em>
				<span>{{ __('member.products') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/message/tree/members/'.$parent->id) }}" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span>{{ __('member.comments') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/construction') }}" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span>{{ __('member.files') }}</span><br />			
		 </a>
		</div>
        
        @php 
			 if ($user->profile_photo_path == '') {
				$user->profile_photo_path = 'https://gravatar.com/avatar/'.
				   md5($user->email).
				   '?d='.urlencode('https://www.pinpng.com/pngs/m/341-3415688_no-avatar-png-transparent-png.png');
			 } else {
			 	$user->profile_photo_path = '/'.$user->profile_photo_path;
			 }        
        @endphp
        
	    <div class="col-11 col-md-10">
	    	<h4>{{ $user->name }}</h4>
	    	<p>{{ implode(',',$ranks) }}</p>
	    	<p><img src="{{ $user->profile_photo_path }}" style="width:30%" /></p>
	    </div>
	    
	  </div> <!-- .row -->
	  <div xlass="row">
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
   </script> 
    
   
   </div>
</x-guest-layout>  
