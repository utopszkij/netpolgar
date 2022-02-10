<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$task->name = \Request::old('name');
			$task->type = \Request::old('type');
			$task->deadline = \Request::old('deadline');
			$task->status = explode(',',\Request::old('status'));
			$task->assign = \Request::old('assign');
		@endphp
	@endif
	@php
		function selected($a,$b) {
			if ($a == $b) {
				$result = ' selected="selected"';
			} else {
				$result = '';
			}
			return $result;	
		}
	@endphp

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($task->id > 0)
                <h2>{{ __('task.edit') }}</h2>
                @else
                <h2>{{ __('task.add') }}</h2>
                @endif
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
 
 	@if ($task->id <= 0)
    <form action="{{ \URL::to('/tasks') }}" method="POST">
   @else
    <form action="{{ \URL::to('/tasks/'.$task->id) }}" method="POST">
   @endif 
   @csrf
      <input type="hidden" name="id" value="{{ $task->id }}" class="form-control" placeholder="">
      <input type="hidden" name="project_id" value="{{ $task->project_id }}" class="form-control" placeholder="">
      @if ($info->userAdmin)
      	<!-- admin -->
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.status') }}:</label>
                    <select name="status">
								<option value="waiting"{{ selected('waiting', $task->status) }}>
									{{ __('task.waiting') }}</option>                    
								<option value="active"{{ selected('active', $task->status) }}>
									{{ __('task.active') }}</option>                    
								<option value="inwork"{{ selected('inwork', $task->status) }}>
									{{ __('task.inwork') }}</option>                    
								<option value="canControl"{{ selected('canControl', $task->status) }}>
									{{ __('task.canControl') }}</option>                    
								<option value="closed"{{ selected('closed', $task->status) }}>
									{{ __('task.closed') }}</option>                    
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.deadline') }}:</label>
                    <input type="date" name="deadline"
                    value="{{ $task->deadline }}" />
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.type') }}:</label>
                    <select name="type">
								<option value="bug"{{ selected('bug', $task->type) }}>
									{{ __('task.bug') }}</option>                    
								<option value="info"{{ selected('info', $task->type) }}>
									{{ __('task.info') }}</option>                    
								<option value="proposal"{{ selected('proposal', $task->type) }}>
									{{ __('task.proposal') }}</option>                    
								<option value="task"{{ selected('task', $task->type) }}>
									{{ __('task.task') }}</option>                    
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.name') }}:</label>
                    <textarea cols="80" rows="5"  name="name">{{ $task->name}}</textarea>
                </div>
            </div>
         </div>
		@else
      	<!-- nem admin -->
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
	                 <label>{{ __('task.status') }}:</label>
               	@if ($task->assign == \Auth::user()->id)
                    <select name="status">
								<option value="waiting"{{ selected('waiting', $task->status) }}>
									{{ __('task.waiting') }}</option>                    
								<option value="active"{{ selected('active', $task->status) }}>
									{{ __('task.active') }}</option>                    
								<option value="inwork"{{ selected('inwork', $task->status) }}>
									{{ __('task.inwork') }}</option>                    
								<option value="canControl"{{ selected('canControl', $task->status) }}>
									{{ __('task.canControl') }}</option>                    
								<option value="closed"{{ selected('closed', $task->status) }}>
									{{ __('task.closed') }}</option>                    
                    </select>
                   @else 
                   	<input type="hidden" name="status" value="{{ $task->status}}" />
                   	{{ $task->status }}
                   @endif 
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.deadline') }}:</label>
                    {{ $task->deadline }}
                    <input type="hidden" name="deadline" value="{{ $task->deadline }}" />
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.type') }}:</label>
                    {{ __('task.'.$task->type) }}
                    <input type="hidden" name="type" value="{{ $task->type }}" />
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.name') }}:</label>
                    {{ $task->name}}
                    <input type="hidden" name="name" value="{{ $task->name }}" />
                </div>
            </div>
         </div>
		@endif   
      <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('task.assign') }}:</label>
                    <select name="assign">
                    	<option value="0"{{ selected('0',$task->assign) }}>&nbsp;</option>
                    	@foreach ($members as $member)
                    	<option value="{{ $member->user_id }}"{{ selected($member->user_id,$task->assign) }}>
                    		{{ $member->name }}
                    	</option>
                    	@endforeach
                    </select>
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
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('task.save') }}
	              </button>
	              @if (($info->userAdmin) & ($task->id > 0))
	              <button class="btn btn-danger"
	              		type="button" onclick="delClick({{ $task->id}})">
	                  <em class="fas fa-eraser"></em>
	                  {{ __('task.delete') }}
	              </button> 
	              @endif
	              <a class="btn btn-secondary" 
	              		href="{{ \URL::to('/projects/'.$project->id) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('task.cancel') }}
	              </a>
	            </div>
	    </div>  
    </form>
    
    <script type="text/javascript">
		function delClick(id) {
			popupConfirm('Biztos törölni akarod?',
			function() {
				location = "{{ \URL::to('/tasks') }}"+"/"+id+"/delete";
			}, 
			true);		
		}    
    </script>
</x-guest-layout>  
