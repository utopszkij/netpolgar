<x-guest-layout>  

	@php 
		 if (($poll->status == 'proposal') | ($poll->status == 'debate')) {
			$statuses = 'proposal-debate';
		 } else {
		 	$statuses = $poll->status;
		 }	
		 
		 function getOptionInfo($option) {
			$model = new \App\Models\Option();
			return $model->getInfo($option);		 
		 }
	@endphp	 

	<div id="pollContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>
                	<a href="{{ \URL::to('/'.$poll->parent_type.'/'.$parent->id) }}">
                		<em class="fas fa-hand-point-right"></em>
							@if ($poll->parent_type == 'teams')
							<em class="fas fa-user-friends"></em>
							@endif
							@if ($poll->parent_type == 'projects')
							<em class="fas fa-cogs"></em>
							@endif
                		&nbsp;{{ $parent->name }}
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
			<var id="subMenuIcon" class="subMenuIcon" 
				onclick="togglePollMenu()">
				&nbsp;<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="{{ \URL::to('/'.$poll->parent_type.'/'.$poll->parent.'/'.$statuses.'/polls') }}">
            <em class="fas fa-reply"></em>
            <span>{{ __('poll.back') }}</span><br />
         </a>
		 <a href="{{ URL::to('/message/tree/polls/'.$poll->id) }}" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span>{{ __('poll.comments') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/'.$poll->id.'/votes') }}" title="Szavazatok">
				<em class="fas fa-list"></em>
				<span>{{ __('poll.votes') }}</span><br />			
		 </a>
		 <a href="{{ URL::to('/'.$poll->id.'/votes/getform') }}" title="Szavazatom">
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

		 <div class="col-11 com-md-10 help">
				 {!! __('poll.help') !!}
		 </div>
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
			<strong> 
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
				<var class="help">{!! __('poll.'.$poll->status.'Help') !!}</var>
			</strong>	
	        	&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
	    <div class="col-11 col-md-10">
            	{!! \App\Models\Minimarkdown::miniMarkdown($poll->description) !!}
		</div>
		
   	@if (($poll->status == 'proposal') & ($info->userMember))
	    <div class="col-11 col-md-10">
   			<a href="{{ \URL::to('/like/polls/'.$poll->id) }}" 
   			   title="a vita megnyitását javaslom">
        	   @if ($info->userLiked)
			   <em class="fas fa-thumbs-up liked"></em>
			   @else
			   <em class="fas fa-thumbs-up"></em>
        	   @endif
        	</a>
        	<a href="{{ \URL::to('/likeinfo/polls/'.$poll->id) }}">
   				({{ $info->likeCount }}/{{ $info->likeReq}})
	      	</a>	
			{{ __('poll.like') }}
       </div>
      @else
		<!-- zavaró ha ki van írva, főleg ha közben regisztráltak igy a likeReq változhatott 
	    <div class="col-11 col-md-10">
     	   <em class="fas fa-thumbs-up"></em>
        	</a>
        	<a href="{{ \URL::to('/likeinfo/polls/'.$poll->id) }}">
   				({{ $info->likeCount }}/{{ $info->likeReq}})
	      </a>	
			{{ __('poll.liked') }}
       </div>
		-->
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
                	Egy opció javaslat akkor kerül a "szavazó lapra" ha a javaslatot a tagok
                	{{ $poll->config->optionActivate }}%-a támogatja.
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>a vita hossza:</label>
   				{{ $poll->config->debateDays }} nap
				@if ($poll->debate_start != '')
				<strong>
				  {{ $poll->debate_start }}
				  &nbsp;-&nbsp;
				  {{  date('Y.m.d', strtotime($poll->debate_start.' + '.($poll->config->debateDays - 1).' days' ))}}
				</strong>
				@endif   
   			</div>
   		</div>
   		<div class="row">
   			<div class="col-12">
   			   	<label>a szavazás hossza:</label>
   				{{ $poll->config->voteDays }} nap
				@if ($poll->debate_start != '')
				<strong>
				   {{  date('Y.m.d', strtotime($poll->debate_start.' + '.($poll->config->debateDays).' days' ))}}
				   &nbsp;-&nbsp;
				   {{  date('Y.m.d', strtotime($poll->debate_start.' + '.($poll->config->debateDays + $poll->config->voteDays - 1).' days' ))}}
				</strong>   
				@endif   
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
			if ($option->description == '') {
				$option->description = $option->name;
			}
			@endphp
		    <li>
		     <em class="fas fa-caret-right"></em>&nbsp;
			  @if ($option->status == 'proposal')
					<strong>{{ __('poll.proposalOption') }}</strong>&nbsp;
			  @endif
			  {!! \App\Models\Minimarkdown::miniMarkdown($option->description) !!}
			  @if ($poll->config->pollType != 'yesno')
				  @if (($userMember) & 
					   ($option->status == 'proposal')) 
					  <a href="{{ \URL::to('/like/options/'.$option->id) }}"> 
						@if ($optionInfo->userLiked)
							<em class="fas fa-thumbs-up liked"></em>
						@else
							<em class="fas fa-thumbs-up"></em>
						@endif 
						</a>
						<a href="{{ \URL::to('/likeinfo/options/'.$option->id) }}">
						{{ $optionInfo->likeCount }} / {{ $optionInfo->likeReq }}
						</a>
						{{ __('poll.optionLike') }}
				  @else
				        <!-- zavaró ha ki van írva (lásd fentebb)
						<em class="fas fa-thumbs-up"></em>
						<a href="{{ \URL::to('/likeinfo/options/'.$option->id) }}">
						{{ $optionInfo->likeCount }} / {{ $optionInfo->likeReq }}
						</a>
						{{ __('poll.optionLiked') }}
						-->
				  @endif				
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
		
		@if (($poll->status == 'vote') & 
		     ($info->userMember) &  
		     (!$info->userVoted)
		    )
			<div class="row">
			<a href="{{ \URL::to('/'.$poll->id.'/votes/create') }}" class="btn btn-primary">
				<em class="fas fa-envelope-open-text"></em>
				{{ __('poll.voteNow') }}
			</a>
		</div>		
		@endif

		@if ((!\Auth::check()) & ($poll->status == 'vote')) 
		<div class="row">
			<a href="{{ \URL::to('/'.$poll->id.'/votes/create') }}" class="btn btn-primary">
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
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-right"></em><br />';
			} else {
				pollMenu.style.width="100%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="inline-block";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-left"></em>&nbsp;';
			}
			return false;	
		}   
   </script> 
    
   
   </div>
</x-guest-layout>  
