<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('task.details') }}</h2>
            </div>
        </div>
    </div>
 
    <div class="row">
    	<h3>
			<a href="{{ \URL::to('/projects/'.$project->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-cogs"></em>
				&nbsp;{{ $project->name }} 			
			</a>
		</h3>	    	
	 </div>    
    <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
	                 <label>{{ __('task.status') }}:</label>
                   	{{ __('task.'.$task->status) }}
                   	@if (($info->userAdmin) | ($info->userMember))
                   	&nbsp;&nbsp;
                   	<a href="{{ \URL::to('/tasks/'.$task->id.'/edit') }}">
							   <em class="fas fa-edit"></em>                	
                   	</a>
                   	@endif
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.deadline') }}:</label>
                    {{ $task->deadline }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.type') }}:</label>
                    {{ __('task.'.$task->type) }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.name') }}:</label>
                    {{ $task->name}}
                </div>
            </div>
         </div>
      <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.assign') }}:</label>
                    {{ $task->assign }}
                </div>
            </div>
      </div>
      @if ($task->id > 0)
      <div class="row">
      	<div class="col-12">
	      	<a href="{{ \URL::to('/like/tasks/'.$task->id) }}" title="tetszik">
				<em class="fas fa-thumbs-up"></em>
				</a> 
	      	<a href="{{ \URL::to('/likeinfo/tasks/'.$task->id) }}">
				{{ $taskInfo->likeCount }}
				</a> 
				&nbsp;&nbsp;
	      	<a href="{{ \URL::to('/dislike/tasks/'.$task->id) }}" title="nem tetszik">
				<em class="fas fa-thumbs-down"></em> 
				</a> 
	      	<a href="{{ \URL::to('/likeinfo/tasks/'.$task->id) }}">
				{{ $taskInfo->disLikeCount }}
				</a> 
				&nbsp;&nbsp;
	      	<a href="{{ \URL::to('/message/tree/tasks/'.$task->id) }}" title="hozzászólások">
				<em class="fas fa-comments"></em> 
				</a> 
				{{ $taskInfo->commentCount }}			      
				</a> 
			</div>
      </div>
      @endif
      <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <a class="btn btn-secondary" 
	              		href="{{ \URL::to('/projects/'.$project->id) }}">
	                  <em class="fas fa-reply"></em>
	                  {{ __('task.close') }}
	              </a>
	            </div>
	    </div>  
    </form>
    
</x-guest-layout>  
