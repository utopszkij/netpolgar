<x-guest-layout>  

   @php if ($team->avatar == '') $team->avatar = URL::to('/').'/img/team.png'; @endphp

	<div id="teamContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('team.details') }}</h2>
            </div>
        </div>
    </div>
    
	<div class="row">
		<div class="col-1 col-md-2" id="teamMenu">
			<var id="subMenuIcon" class="subMenuIcon" onclick="toggleTeamMenu()">
				<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="{{ route('parents.teams.index', $team->parent) }}">
            <em class="fas fa-reply"></em>
            <span>{{ __('team.back') }}</span><br />
         </a>
			<a href="{{ URL::to('/member/list/teams/'.$team->id) }}" title="Tagok">
				<em class="fas fa-users"></em>
				<span>{{ __('team.members') }}</span><br />			
			</a>
			<a href="{{ route('parents.teams.index', $team->id) }}" title="Tagok">
				<em class="fas fa-sitemap"></em>
				<span>{{ __('team.subGroups') }}</span><br />			
			</a>
			<a href="{{ URL::to('/'.$team->id.'/projects') }}" title="Projektek">
				<em class="fas fa-cogs"></em>
				<span>{{ __('team.projects') }}</span><br />			
			</a>
			<a href="{{ URL::to('/construction') }}" title="Termékek">
				<em class="fas fa-shopping-basket"></em>
				<span>{{ __('team.products') }}</span><br />			
			</a>
		    <a href="{{ URL::to('/message/tree/teams/'.$team->id) }}" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span>{{ __('team.comments') }}</span><br />			
			</a>
			<a href="{{ URL::to('/teams/'.$team->id.'/proposal-debate/polls')  }}" title="Viták">
				<em class="fas fa-retweet"></em>
				<span>{{ __('team.debates') }}</span><br />			
			</a>
			<a href="{{ URL::to('/teams/'.$team->id.'/vote/polls') }}" title="szavazások">
				<em class="fas fa-balance-scale-left"></em>
				<span>{{ __('team.polls') }}</span><br />			
			</a>
			<a href="{{ URL::to('/teams/'.$team->id.'/closed/polls') }}" title="Döntések">
				<em class="fas fa-check"></em>
				<span        		    
				>{{ __('team.decisions') }}</span><br />			
			</a>
			<a href="{{ URL::to('/construction') }}" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span>{{ __('team.files') }}</span><br />			
			</a>
			<a href="{{ URL::to('/construction') }}" title="Események">
				<em class="fas fa-calendar"></em>
				<span>{{ __('team.events') }}</span><br />			
			</a>
		</div>
		
		<div class="col-11 col-md-10" id="teamBody">
		    <div class="col-11 col-md-10 path" style="margin-top: 5px;">
		    @php $pathSeparator = ''; @endphp
		    @foreach ($info->path as $item)
		    	@if ($item->id != $team->id)
		    	<var class="pathItem">
					<a href="{{ route('teams.show',["team" => $item->id]) }}">
						<em class="fas fa-hand-point-right"></em>
						&nbsp;{!! $pathSeparator !!}&nbsp;{{ $item->name }} 			
					</a>    	
		    	</var>
		    	@endif
			   @php $pathSeparator = '<em class="fas fa-caret-right"></em>'; @endphp
			 @endforeach	    
			 </div>    


	       <div class="col-11 col-md-10">
             <h3>
             	{{ $team->name }}
		        @if ((in_array('active_admin',$info->userRank)) & ($team->status != 'closed'))
	            &nbsp;<a href="{{ route('teams.edit',['team' => $team->id]) }} ">
						<em class="fas fa-edit" title="{{ __('team.edit') }}"></em>                
   	            @endif
   	          </a>
             </h3>
         </div>

        	<div class="col-11 col-md-10">
             	@if ($team->status == 'active')
             	<em class="fas fa-check"></em>
             	@endif
             	@if ($team->status == 'proposal')
             	<em class="fas fa-question"></em>
             	@endif
             	@if ($team->status == 'closed')
             	<em class="fas fa-lock"></em>
             	@endif
	        	{{ __('team.'.$team->status) }}
	        	&nbsp;&nbsp;&nbsp;&nbsp;
        		@if (count($info->userRank) > 0)
        		@php 
        		$info->transUserRank = [];
				for ($i=0; $i<count($info->userRank); $i++) {
					$info->transUserRank[$i] = __('team.'.$info->userRank[$i]);				
				}        		
        		@endphp 
        		{{ implode(',',$info->transUserRank) }} vagy&nbsp;
        		@endif
        		@if ((count($info->userRank) == 0) & ($team->status == 'active'))
        			<form action="{{ URL::to('/member/store') }}"
        				style="display:inline-block; width:auto">
        			<input type="hidden" name="parent_type" value="teams" />
        			<input type="hidden" name="parent" value="{{ $team->id }}" />
        			<input type="hidden" name="rank" value="member" />
        			<button type="submit" class="btn btn-primary" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-sign-in-alt"></em>
						{{ __('team.signin') }}        				
        			</button>
        			</form>
        		@endif
        		@if ((count($info->userRank) > 0) & 
        		     ($team->status == 'active') & ($team->id != 1))
        			<form action="{{ URL::to('/member/doexit') }}"
        				style="display:inline-block; width:auto">
        			<input type="hidden" name="parent_type" value="teams" />
        			<input type="hidden" name="parent" value="{{ $team->id }}" />
        			<input type="hidden" name="rank" value="member" />
        			<button type="submit" class="btn btn-primary" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-sign-out-alt"></em>
						{{ __('team.signout') }}        				
        			</button>
        			</form>
        		@endif
        		@if ((in_array('active_member', $info->userRank) | in_array('active_admin',$info->userRank)) & 
        		     ($team->status == 'active') & ($team->id != 1) ) 
        			<a class="btn btn-danger" 
        			   href="{{ \URL::to('/dislike/teams/'.$team->id) }}" 
        			   title="a csoport lezárását javaslom">
        				@if ($info->userDisLiked)
        				<em class="fas fa-check"></em>
        				@endif
        				<em class="fas fa-thumbs-down"></em>
        				<a href="{{ \URL::to('/likeinfo/teams/'.$team->id) }}">
	        				({{ $info->disLikeCount }}/{{ $info->disLikeReq}})
        				</ä>a>
						{{ __('team.dislike') }}
        			</a>
        		@endif
        		@if ((count($info->userParentRank) > 0) & ($team->status == 'proposal'))
        			<a class="btn btn-success" 
        			   href="{{ \URL::to('/like/teams/'.$team->id) }}" 
        			   title="a csoport aktiválását javaslom">
        				@if ($info->userLiked)
        				<em class="fas fa-check"></em>
        				@endif
        				<em class="fas fa-thumbs-up"></em>
        				<a href="{{ \URL::to('/likeinfo/teams/'.$team->id) }}">
	        				({{ $info->likeCount }}/{{ $info->likeReq}})
	        			</a>	
						{{ __('team.like') }}
        			</a>
        		@endif
        </div>
        
	     <div class="col-11 col-md-10">
				<img src="{{ $team->avatar }}" alt="logo" title="logo"
					style="float:right; width:25%" />        		
            <div style="width:70%">
            	{!! str_replace("\n",'<br />',$team->description) !!}
            	<h4>Beállítások</h4>
					<div class="config" style="display:inline-block; width:500px">
						  tisztségek:  {{ implode(',',$team->config->ranks) }}<br />	
						  {{ $team->config->close }}
						  % támogatottság kell a csoport lezárásához,<br />
						  {{ $team->config->memberActivate }}
						  fő támogató kell tag felvételéhez,<br />
						  {{ $team->config->memberExclude }}
						  % támogatottság kell tag kizárásához,<br />
						  {{ $team->config->rankActivate }}	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  {{ $team->config->rankClose }}
						  % támogatottság kell tisztség visszavonásához,<br />
						  {{ $team->config->projectActivate }}
						  fő támogató kell projekt aktiválásához,<br />
						  {{ $team->config->productActivate }} 
						  % támogatottság kell termék közzé tételéhez,<br />
						  {{ $team->config->subTeamActivate }}
						  fő támogató kell alcsoport aktiválásához,<br />
						  {{ $team->config->debateActivate }}
						  fő támogató kell eldöntendő vita inditásához
					</div>
            </div>
	     </div>
		</div> <!-- .row -->
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
