<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$project->name = \Request::old('name');
			$project->description = \Request::old('description');
			$project->avatar = \Request::old('avatar');
			$project->deadline = \Request::old('deadline');
			$project->config->ranks = explode(',',\Request::old('ranks'));
			$project->config->close = \Request::old('close');
			$project->config->memberActivate = \Request::old('memberActivate');
			$project->config->memberExclude = \Request::old('memberExclude');
			$project->config->rankActivate = \Request::old('rankActivate');
			$project->config->rankClose = \Request::old('rankClose');
			$project->config->debateActivate = \Request::old('debateActivate');

		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($project->id > 0)
                <h2>{{ __('project.edit') }}</h2>
                @else
                <h2>{{ __('project.add') }}</h2>
                @endif
            </div>
        </div>
    </div>
 
    <div class="row">
			<a href="{{ \URL::to('/teams/'.$team->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-user-friends"></em>
				&nbsp;{{ $team->name }} 			
			</a>    	
	 </div>    
 
 	@if ($project->id <= 0)
    <form action="{{ \URL::to('/projects') }}" method="POST">
   @else
    <form action="{{ \URL::to('/projects/'.$project->id) }}" method="POST">
   @endif 
   @csrf
         <input type="hidden" name="id" value="{{ $project->id }}" class="form-control" placeholder="">
         <input type="hidden" name="team_id" value="{{ $project->team_id }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('project.status') }}:</label>
                    {{ __('project.'.$project->status) }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('project.deadline') }}:</label>
                    <input type="date" name="deadline"
                    value="{{ $project->deadline }}" />
                </div>
            </div>
         </div>
         
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('project.name') }}:</label>
                    <input type="text" name="name" style="width:600px"
                     value="{{ $project->name }}" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('project.avatar') }}:</label>
                    <input type="text" name="avatar" value="{{ $project->avatar }}" class="form-control" placeholder="URL">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('project.description') }}
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! $project->description !!}</textarea>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label style="vertical-align: top;">Beállítások:</label>
						<div class="config" style="display:inline-block; width:500px">
						  <br />Tisztségek:  <input type="text" 
						  style="width:400px" 
						  name="ranks" value="{{ implode(',',$project->config->ranks) }}" />
						  <br />
						  <input type="number" min="1" max="100" 
						  name="close" value="{{ $project->config->close }}" />
						  % támogatottság kell a projekt lezárásához,<br />
						  <input type="number" min="0" max="100" 
						  name="memberActivate" value="{{ $project->config->memberActivate }}" />
						  fő támogató kell tag felvételéhez,<br />
						  <input type="number" min="1" max="100" 
						  name="memberExclude" value="{{ $project->config->memberExclude }}" />
						  % támogatottság kell tag kizárásához,<br />
						  <input type="number" min="1" max="100" 
						  name="rankActivate" value="{{ $project->config->rankActivate }}" />	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  <input type="number" min="1" max="100" 
						  name="rankClose" value="{{ $project->config->rankClose }}" />
						  % támogatottság kell tisztség visszavonásához,<br />
						  <input type="number" min="0" max="100" 
						  name="debateActivate" value="{{ $project->config->debateActivate }}" />
						  fő támogatás kell vita inditásához
						</div>
                </div>
               </div> 
            </div>
	         <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('project.save') }}
	              </button>
	              <a class="btn btn-secondary" 
	              		href="{{ \URL::to('/teams/'.$team->id) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('project.cancel') }}
	              </a>
	            </div>
	         </div>  
        </div>
   
    </form>
</x-guest-layout>  
