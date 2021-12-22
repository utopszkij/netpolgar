<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$team->name = \Request::old('name');
			$team->description = \Request::old('description');
			$team->avatar = \Request::old('avatar');
			$team->config->ranks = explode(',',\Request::old('ranks'));
			$team->config->close = \Request::old('close');
			$team->config->memberActivate = \Request::old('memberActivate');
			$team->config->memberExclude = \Request::old('memberExclude');
			$team->config->rankActivate = \Request::old('rankActivate');
			$team->config->rankClose = \Request::old('rankClose');
			$team->config->projectActivate = \Request::old('projectActivete');
			$team->config->productActivate = \Request::old('productActivate');
			$team->config->subTeamActivate = \Request::old('subTeamActivate');
			$team->config->debateActivate = \Request::old('debateActivate');

		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($team->id > 0)
                <h2>{{ __('team.edit') }}</h2>
                @else
                <h2>{{ __('team.add') }}</h2>
                @endif
            </div>
        </div>
    </div>
 
    <div class="row path" style="margin-top: 5px;">
    @php $pathSeparator = ''; @endphp
    @foreach ($info->path as $item)
    	@if ($item->id != $team->id)
    	<var class="pathItem">
			<a href="{{ route('teams.show',["team" => $item->id]) }}">
				&nbsp;{!! $pathSeparator !!}&nbsp;{{ $item->name }} 			
			</a>    	
    	</var>
    	@endif
	   @php $pathSeparator = '<em class="fas fa-caret-right"></em>'; @endphp
	 @endforeach	    
	 </div>    
 
 	@if ($team->id > 0)
    <form action="{{ route('teams.update',$team->id) }}" method="POST">
   @else
    <form action="{{ route('parents.teams.store', $team->parent) }}" method="POST">
   @endif 
   @csrf
 	@if ($team->id > 0)
     @method('PUT')
	@endif   
         <input type="hidden" name="id" value="{{ $team->id }}" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="{{ $team->parent }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('team.status') }}:</label>
                    {{ __('team.'.$team->status) }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('team.name') }}:</label>
                    <input type="text" name="name" value="{{ $team->name }}" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('team.avatar') }}:</label>
                    <input type="text" name="avatar" value="{{ $team->avatar }}" class="form-control" placeholder="URL">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('team.description') }}
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! $team->description !!}</textarea>
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
						  name="ranks" value="{{ implode(',',$team->config->ranks) }}" />
						  <br />
						  <input type="number" min="1" max="100" 
						  name="close" value="{{ $team->config->close }}" />
						  % támogatottság kell a csoport lezárásához,<br />
						  <input type="number" min="0" max="100" 
						  name="memberActivate" value="{{ $team->config->memberActivate }}" />
						  fő támogató kell tag felvételéhez,<br />
						  <input type="number" min="1" max="100" 
						  name="memberExclude" value="{{ $team->config->memberExclude }}" />
						  % támogatottság kell tag kizárásához,<br />
						  <input type="number" min="1" max="100" 
						  name="rankActivate" value="{{ $team->config->rankActivate }}" />	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  <input type="number" min="1" max="100" 
						  name="rankClose" value="{{ $team->config->rankClose }}" />
						  % támogatottság kell tisztség visszavonásához,<br />
						  <input type="number" min="1" max="100" 
						  name="projectActivate" value="{{ $team->config->projectActivate }}" />
						  fő támogató kell projekt aktiválásához,<br />
						  <input type="number" min="1" max="100" 
						  name="productActivate" value="{{ $team->config->productActivate }}" />
						  % támogatottság kell termék közzé tételéhez,<br />
						  <input type="number" min="0" max="100" 
						  name="subTeamActivate" value="{{ $team->config->subTeamActivate }}" />
						  fő támogató kell alcsoport aktiválásához,<br />
						  <input type="number" min="0" max="100" 
						  name="debateActivate" value="{{ $team->config->debateActivate }}" />
						  fő támogató kell eldöntendő vita inditásához
						</div>
                </div>
               </div> 
            </div>
	         <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('team.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ route('parents.teams.index',["parent" => $team->parent]) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('team.cancel') }}
	              </a>
	            </div>
	         </div>  
        </div>
   
    </form>
</x-guest-layout>  
