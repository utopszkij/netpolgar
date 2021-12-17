<x-guest-layout>  

	@php 
		 if (($poll->status == 'proposal') | ($poll->status == 'debate')) {
			$statuses = 'proposal-debate';
		 } else {
		 	$statuses = $poll->status;
		 }	
		 
		 function getOptionInfo($option) {
			$result = JSON_decode('{"likeCount":0, "likeReq":1, "userLiked":true}');
			$poll = \Db::table('polls')->where('id','=',$option->poll_id)
				->first();
			$poll->config = JSON_decode($poll->config);
			if (!isset($poll->config->optionActivate)) {
				$poll->config->optionActivate = 2;			
			}	
			$memberCount = \Db::table('members')
				->select('user_id')
				->where('parent_type','=',$poll->parent_type)
				->where('parent','=',$poll->parent)
				->where('status','=','active')
				->groupBy('user_id')
				->count();
			$result->likeReq = round($memberCount * $poll->config->optionActivate / 100);	
			$user = \Auth::user();
			if ($user) {
				$result->likeCount = \Db::table('likes')
				->where('parent_type','=','options')
				->where('parent','=',$option->id)
				->where('like_type','=','like')
				->count();
				$result->userLiked = (\Db::table('likes')
				->where('parent_type','=','options')
				->where('parent','=',$option->id)
				->where('like_type','=','like')
				->where('user_id','=',$user->id)
				->count() > 0);
			} 
			return $result;		 
		 }
	@endphp	 

	<div id="pollContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>
                	<a href="{{ \URL::to('/'.$poll->parent_type.'/'.$parent->id) }}">
                		<em class="fas fa-hand-point-right"></em>
                		{{ $parent->name }}
                	</a>
                </h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('poll.'.$poll->status) }} {{ __('poll.details') }}</h2>
            </div>
        </div>
    </div>
    
	<div class="row">
		<div class="col-1 col-md-2" id="pollMenu">
			<var id="subMenuIcon" class="subMenuIcon" onclick="togglePollMenu()">
				<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="{{ \URL::to('/'.$poll->parent_type.'/'.$poll->parent.'/'.$statuses.'/polls') }}">
            <em class="fas fa-reply"></em>
            <span>{{ __('poll.back') }}</span><br />
         </a>
		 <a href="{{ URL::to('/message/tree/poll/'.$poll->id) }}" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span>{{ __('poll.comments') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/polls/'.$poll->id.'/votes') }}" title="Beszégetés">
				<em class="fas fa-list"></em>
				<span>{{ __('poll.votes') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/polls/'.$poll->id.'/votes') }}" title="Beszégetés">
				<em class="fas fa-search"></em>
				<span>{{ __('poll.check') }}</span><br />			
		 </a>
		 
		 <a href="{{ URL::to('/construction') }}" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span>{{ __('poll.files') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/construction') }}" title="Események">
				<em class="fas fa-calendar"></em>
				<span>{{ __('poll.events') }}</span><br />			
		 </a>
		</div>
		
		<div class="col-11 col-md-10" id="pollBody">

	      <div class="col-11 col-md-10">
             <h3>
             	{{ $poll->name }}
             	@if (($info->userAdmin) & 
             		 ($poll->status == 'proposal') | ($poll->status == 'debate'))
	            &nbsp;<a href="{{ \URL::to('/polls/'.$poll->id.'/edit') }} ">
						<em class="fas fa-edit" title="{{ __('poll.edit') }}"></em>
				@endif		                
   	          </a>
             </h3>
         </div>

         <div class="col-11 col-md-10">
             	@if ($poll->status == 'proposal')
             	<em class="fas fa-question"></em>
             	@endif
             	@if ($poll->status == 'debate')
             	<em class="fas fa-retweet"></em>
             	@endif
             	@if ($poll->status == 'vote')
             	<em class="fas fa-balance-scale-left"></em>
             	@endif
             	@if ($poll->status == 'declined')
             	<em class="fas fa-check"></em>
             	@endif
             	@if ($poll->status == 'closed')
             	<em class="fas fa-lock"></em>
             	@endif
	        	{{ __('poll.'.$poll->status) }}
	        	&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
	    <div class="col-11 col-md-10">
            	{!! str_replace("\n",'<br />',$poll->description) !!}
		</div>
		
   	@if ($poll->status == 'proposal')
	    <div class="col-11 col-md-10">
   			<a href="{{ \URL::to('/like/polls/'.$poll->id) }}" 
   			   title="a vita megnyitását javaslom">
        	   @if ($info->userLiked)
        			<em class="fas fa-check"></em>
        	   @endif
        	   <em class="fas fa-thumbs-up"></em>
        	</a>
        	<a href="{{ \URL::to('/likeinfo/polls/'.$poll->id) }}">
   				({{ $info->likeCount }}/{{ $info->likeReq}})
	        </a>	
			{{ __('poll.like') }}
        </div>		
   		@endif
   		
   		<div class="row">
   			<div class="col-12">
   				<h3>{{ __('poll.config') }}</h3>
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>{{ __('poll.pollType') }}:</label>
   				{{ __('poll.'.$poll->config->pollType) }}
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>{{ __('poll.secret') }}:</label>
   				{{ __('poll.'.$poll->config->secret) }}
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>{{ __('poll.liquied') }}:</label>
   				{{ __('poll.'.$poll->config->liquied) }}
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
                	A vita akkor indul meg ha a javaslatot a tagok
                	{{ $poll->config->debateStart }}%-a támogatja.
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
                	Egy opció javaslat akkor kerül a "svazó lapra" ha a javaslatot a tagok
                	{{ $poll->config->optionActivate }}%-a támogatja.
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>a vita hossza:</label>
   				{{ $poll->config->debateDays }} nap
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>a szavazás hossza:</label>
   				{{ $poll->config->voteDays }} nap
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>érvényességi küszöb:</label>
   				{{ $poll->config->valid }} %-os részvétel
   			</div>
   		</div>
		
		<div class="row">
			<h3>{{ __('poll.options') }}</h3>
		</div>
		<div class="row">
		<ol>
		@foreach ($options as $option)
			@php 
			$optionInfo = getOptionInfo($option); 
			@endphp
		    <li>
		     <em class="fas fa-caret-right"></em>&nbsp;
			  @if ($option->status == 'proposal')
					<strong>{{ __('poll.proposalOption') }}</strong>&nbsp;
			  @endif
			  {{ $option->name }}
			  @if (($userMember) & 
			       ($option->status == 'proposal')) 
			  		@if ($optionInfo->userLiked)
			  			<em class="fas fa-thumbs-up liked"></em>
			  		@else
			  			<em class="fas fa-thumbs-up"></em>
			  		@endif 
			  		{{ $optionInfo->likeCount }} / {{ $optionInfo->likeReq }}
			  		{{ __('poll.optionLike') }}
			  @endif					  
			  @if ($userAdmin)
			  <a href="{{ \URL::to('/options/'.$option->id.'/edit') }}">
				<em class="fas fa-edit"></em>			  
			  </a>
			  @endif
			</li>  
		@endforeach
		</ul>
		</div>
		
		@if ($poll->config->pollType != 'yesno')
		@if ((($poll->status == 'debate') & ($info->userMember)) |
		     (($poll->status == 'proposal') & ($info->userAdmin)))
		<div class="row">
			<a href="{{ \URL::to('/'.$poll->id.'/options/create') }}" class="btn btn-primary">
				<em class="fas fa-plus"></em>
				{{ __('poll.addOption') }}
			</a>
		</div>
		@endif		
		@endif
		
		@if (($poll->status == 'vote') & ($info->userMember) & (!$info->userVoted))
		<div class="row">
			<a href="" class="btn btn-primary">
				<em class="fas fa-envelope-open-text"></em>
				{{ __('poll.voteNow') }}
			</a>
		</div>		
		@endif
		
		@if (($poll->status == 'closed') | ($poll->status == 'vote'))
		@include('poll.result',["poll" => $poll])
		@endif
		
	</div>    
    
   <script>
		function togglePollMenu() {
			var pollMenu = document.getElementById('pollMenu');
			if (pollMenu.style.width == "100%") {
				pollMenu.style.width="8.3%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="none";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-right"></em>';
			} else {
				pollMenu.style.width="100%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="inline-block";
				} 	
				document.getElementById('pollMenuIcon').innerHTML = '<em class="fas fa-caret-left"></em>';
			}
			return false;	
		}   
   </script> 
    
   
   </div>
</x-guest-layout>  
