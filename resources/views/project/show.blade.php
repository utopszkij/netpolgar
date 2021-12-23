<x-guest-layout>  

   @php if ($project->avatar == '') $project->avatar = URL::to('/').'/img/project.png'; @endphp
team
	<div id="projectContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('project.details') }}</h2>
            </div>
        </div>
    </div>
    
	<div class="row">
		<div class="col-1 col-md-2" id="projectMenu">
			<var id="subMenuIcon" class="subMenuIcon" onclick="toggleProjectMenu()">
				<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="{{ \URL::to('/teams/'.$team->id) }}">
            <em class="fas fa-reply"></em>
            <span>{{ __('project.back') }}</span><br />
         </a>
			<a href="{{ URL::to('/member/list/projects/'.$project->id) }}" title="Tagok">
				<em class="fas fa-users"></em>
				<span>{{ __('project.members') }}</span><br />			
			</a>
		    <a href="{{ URL::to('/message/tree/projects/'.$project->id) }}" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span>{{ __('project.comments') }}</span><br />			
			</a>
			<a href="{{ URL::to('/projects/'.$project->id.'/proposal-debate/polls')  }}" title="Viták">
				<em class="fas fa-retweet"></em>
				<span>{{ __('project.debates') }}</span><br />			
			</a>
			<a href="{{ URL::to('/projects/'.$project->id.'/vote/polls') }}" title="szavazások">
				<em class="fas fa-balance-scale-left"></em>
				<span>{{ __('project.polls') }}</span><br />			
			</a>
			<a href="{{ URL::to('/projects/'.$project->id.'/closed/polls') }}" title="Döntések">
				<em class="fas fa-check"></em>
				<span        		    
				>{{ __('project.decisions') }}</span><br />			
			</a>
			<a href="{{ URL::to('/construction') }}" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span>{{ __('project.files') }}</span><br />			
			</a>
			<a href="{{ URL::to('/construction') }}" title="Események">
				<em class="fas fa-calendar"></em>
				<span>{{ __('project.events') }}</span><br />			
			</a>
		</div>
		
		<div class="col-11 col-md-10" id="projectBody">
		    <div class="col-11 col-md-10 path" style="margin-top: 5px;">
					<a href="{{ \URL::to('/teams/'.$team->id) }}">
						<em class="fas fa-hand-point-right"></em>
						&nbsp;{{ $team->name }} 			
					</a>    	
			 </div>    


	       <div class="col-11 col-md-10">
             <h3>
             	{{ $project->name }}
		        @if ((in_array('active_admin',$info->userRank)) & ($project->status != 'closed'))
	            &nbsp;<a href="{{ \URL::to('/projects/'.$project->id.'/edit') }} ">
						<em class="fas fa-edit" title="{{ __('project.edit') }}"></em>                
   	            @endif
   	          </a>
             </h3>
         </div>

        	<div class="col-11 col-md-10">
             	@if ($project->status == 'active')
             	<em class="fas fa-check"></em>
             	@endif
             	@if ($project->status == 'proposal')
             	<em class="fas fa-question"></em>
             	@endif
             	@if ($project->status == 'closed')
             	<em class="fas fa-lock"></em>
             	@endif
	        	{{ __('project.'.$project->status) }}
	        	&nbsp;&nbsp;&nbsp;&nbsp;
        		@if (count($info->userRank) > 0)
        		@php 
        		$info->transUserRank = [];
				for ($i=0; $i<count($info->userRank); $i++) {
					$info->transUserRank[$i] = __('project.'.$info->userRank[$i]);				
				}        		
        		@endphp 
        		{{ implode(',',$info->transUserRank) }} vagy&nbsp;
        		@endif
        		@if ((count($info->userRank) == 0) & ($project->status == 'active'))
        			<form action="{{ URL::to('/member/store') }}"
        				style="display:inline-block; width:auto">
        			<input type="hidden" name="parent_type" value="projects" />
        			<input type="hidden" name="parent" value="{{ $project->id }}" />
        			<input type="hidden" name="rank" value="member" />
        			<button type="submit" class="btn btn-primary" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-sign-in-alt"></em>
						{{ __('project.signin') }}        				
        			</button>
        			</form>
        		@endif
        		@if ((count($info->userRank) > 0) & 
        		     ($project->status == 'active') & ($project->id != 1))
        			<form action="{{ URL::to('/member/doexit') }}"
        				style="display:inline-block; width:auto">
        			<input type="hidden" name="parent_type" value="projects" />
        			<input type="hidden" name="parent" value="{{ $project->id }}" />
        			<input type="hidden" name="rank" value="member" />
        			<button type="submit" class="btn btn-primary" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-sign-out-alt"></em>
						{{ __('project.signout') }}        				
        			</button>
        			</form>
        		@endif
        		@if ((in_array('active_member', $info->userRank) | in_array('active_admin',$info->userRank)) & 
        		     ($project->status == 'active') & ($project->id != 1) ) 
        			<a class="btn btn-danger" 
        			   href="{{ \URL::to('/dislike/projects/'.$project->id) }}" 
        			   title="a csoport lezárását javaslom">
        				@if ($info->userDisLiked)
        				<em class="fas fa-check"></em>
        				@endif
        				<em class="fas fa-thumbs-down"></em>
        				<a href="{{ \URL::to('/likeinfo/projects/'.$project->id) }}">
	        				({{ $info->disLikeCount }}/{{ $info->disLikeReq}})
        				</ä>a>
						{{ __('project.dislike') }}
        			</a>
        		@endif
        		@if ((count($info->userParentRank) > 0) & ($project->status == 'proposal'))
        			<a class="btn btn-success" 
        			   href="{{ \URL::to('/like/projects/'.$project->id) }}" 
        			   title="a csoport aktiválását javaslom">
        				@if ($info->userLiked)
        				<em class="fas fa-check"></em>
        				@endif
        				<em class="fas fa-thumbs-up"></em>
        				<a href="{{ \URL::to('/likeinfo/projects/'.$project->id) }}">
	        				({{ $info->likeCount }}/{{ $info->likeReq}})
	        			</a>	
						{{ __('project.like') }}
        			</a>
        		@endif
        </div>
	     <div class="col-11 col-md-10">
	     		{{ __('project.deadline') }}: {{ $project->deadline }}
		  </div>	        
	     <div class="col-11 col-md-10">
				<img src="{{ $project->avatar }}" alt="logo" title="logo"
					style="float:right; width:25%" />    
            <div style="width:70%">
            	{!! str_replace("\n",'<br />',$project->description) !!}
            	<h4>Beállítások</h4>
					<div class="config" style="display:inline-block; width:500px">
						  tisztségek:  {{ implode(',',$project->config->ranks) }}<br />	
						  {{ $project->config->close }}
						  % támogatottság kell a csoport lezárásához,<br />
						  {{ $project->config->memberActivate }}
						  fő támogató kell tag felvételéhez,<br />
						  {{ $project->config->memberExclude }}
						  % támogatottság kell tag kizárásához,<br />
						  {{ $project->config->rankActivate }}	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  {{ $project->config->rankClose }}
						  % támogatottság kell tisztség visszavonásához,<br />
						  {{ $project->config->debateActivate }}
						  fő támogató kell eldöntendő vita inditásához
					</div>
            </div>
	     </div>
		</div> <!-- .row -->
	</div>    
    
   <!-- task can-ban table -->
   <style type="text/css">
   	.states {margin: 10px 0px 0px 5px}
		.state {min-height:500px; width:19%; min-width:200px; padding:4px; 
			border-style:solid; border-width:1px; border-color:black;}
		.state h2 {text-align:center;}	 
		task {display:block; min-height:80px; 
		   padding:2px; margin-top:2px;
			border-style:solid; border-width:1px; border-color:black;
			background-color:#c0d0c0;
			opacity:1; z-index:1; cursor:pointer}
		id {display:inline-block; width:auto; font-weight:bold}
		title {display:block; font-weight:bold}  
		desc {display:block;}
		type {display:inline-block; width:auto}  
		assign {display:inline-block; width:auto}  
		req {display:inline-block; width:auto; font-weight:bold}
   </style>
   <div class="row states" id="states">
   	<div class="state waiting" id="waiting">
   		<h2>Várakoztatva</h2>
				<task id="1">
			  		<p>#<id>1</id> Határidő: <deadLine>2021.12.31.</deadLine></p>
			  		<title>első task</title>
			  		<desc>első task leírása</desc>
			  		<p>
			  			<type class="bug">&nbsp;</type>
			  			<assign><img src="" avatar="" title=""/></assign>
			  		</p>
				</task>    		
   	</div>
   	<div class="col-2 state active" id="active">
   		<h2>Megkezdhető</h2>
				<task id="2">
			  		<p>#<id>2</id> Határidő: <deadLine>2022.12.31.</deadLine></p>
			  		<title>második task</title>
			  		<desc>második task leírása</desc>
			  		<p>
			  			<type class="bug">&nbsp;</type>
			  			<assign><img src="" avatar="" title=""/></assign>
			  		</p>
				</task>    		
   	</div>
   	<div class="col-2 state inwork" id="inwork">
   		<h2>Munkában</h2>
   	</div>
   	<div class="col-2 state canControl" id="canControl">
   		<h2>Ellenörizendő</h2>
   	</div>
   	<div class="col-2 state closed" id="closed">
   		<h2>Kész</h2>
   	</div>
   </div> 
   <div style="display:none">
		<iframe id="hideIfrm" name="hideIfrm"></iframe>   
   </div>

   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
   <script>
		var atDragging = false;   
		var members = [
			[0,"nick0","avatar0"],
			[1,"nick1","avatar1"]
		];   
		function toggleProjectMenu() {
			var projectMenu = document.getElementById('projectMenu');
			if (projectMenu.style.width == "100%") {
				projectMenu.style.width="8.3%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="none";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-right"></em>';
			} else {
				projectMenu.style.width="100%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="inline-block";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-left"></em>';
			}
			return false;	
		}
  
     	function taskDrop(event,ui) {
				console.log(event);
				console.log(ui);
				if (ui.offset.top < $('#active').position().top) {
					console.log('out of range');
					return;
				}
				if (atDragging) {
					return;				
				}
				
				// calculate newState
				var newState = '';
				if (ui.offset.left < $('#active').position().left) {
					newState = 'waiting';
				} else if (ui.offset.left < $('#inwork').position().left) {
					newState = 'active';
				} else if (ui.offset.left < $('#canControl').position().left) {
					newState = 'inwork';
				} else if (ui.offset.left < $('#closed').position().left) {
					newState = 'canControl';
				} else {
					newState = 'closed';
				}	
				
				// calculate beforSelector
				var beforeSelector = 'h2';
				var tasks = $('#'+newState).find('task');
				var i;
				for (i=0; i < tasks.length; i++) {
					if (ui.offset.top > $('#'+tasks[i].id).position().top) {
						beforeSelector = '#'+tasks[i].id;
					}			
				}	

		      ui.draggable.insertAfter('#'+newState+' '+beforeSelector);
		      ui.draggable[0].style.left = '0px';	
		      ui.draggable[0].style.top = '0px';	

				// tárol adatbázisba
				$('#hideIfrm').src = "{{ \URL::to('/') }}" +'/tasks/'+ui.draggable.id +
				  '/edit?status='+newState;
				
     	}

		$(function() {
			// init
			
			if (window.innerWidth < 1100) {
				$('#states').css('display','none');
				alert('A task menedzser funkció csak 1100px -nél szélesebb képernyőn müködik!');
			}
			if ($('body').droppable != undefined) {
		    	$('body').droppable({drop: taskDrop});
   		}
		 	if ($('task').draggable != undefined) {
        		$('task').draggable({drop: taskDrop}); 
       	}
       	$('task').mousedown(function(){
	      	atDragging = true;
				this.style.zIndex = 99;      
      	});
      	$('task').mouseup(function(){
      		atDragging = false;
				this.style.zIndex = 1;
      	});
      	$('task').click(function() {
      		console.log('task click', this);
      		// képerníő megjelenítése
				// taskId = $('#taskId').val(this.id);
				// taskAssigned = $('#taskAssigned').val();
				// taskType = $('#taskType').val();      	
				// taskDeadLine = $('#taskDeadLine').val();      	
      		// users select opciók
      		
      		// OK click: modosit a task dom elemet és
      		// tárol adatbáziba
      	});
      	/*
      	$('#taskFormOk').cick(function() {
				var taskId = $('#taskId').val();
				var taskAssigned = $('#taskAssigned').val();
				var taskType = $('#taskType').val();      	
				var taskDeadLine = $('#taskDeadLine').val();      	
				// task dom elem modositása (avatar!)
				
				// tárolás adatbázisba a rejtett iframe -t használva
				
      	});
      	*/
      });  
   </script> 
    
   
   </div>
</x-guest-layout>  
