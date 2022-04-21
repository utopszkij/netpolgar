<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$poll->parent_type = \Request::old('parent_type');
			$poll->parent = \Request::old('parent');
			$poll->name = \Request::old('name');
			$poll->status = \Request::old('status');
			$poll->description = \Request::old('description');
			$poll->config->pollType = \Request::old('pollType');
			$poll->config->secret = \Request::old('secret');
			$poll->config->liquied = \Request::old('liquied');
			$poll->config->debateStart = \Request::old('debateStart');
			$poll->config->optionActivate = \Request::old('optionActivate');
			$poll->config->debateDays = \Request::old('debateDays');
			$poll->config->voteDays = \Request::old('voteDays');
			$poll->config->valid = \Request::old('valid');
			
		@endphp
	@endif

	@php
		function selected($value, $actual) {
				$result = '';
				if ($value == $actual) {
					$result = ' selected="selected"';
				}
				return $result;
		}
	@endphp

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($poll->id > 0)
                <h2>{{ __('poll.edit') }}</h2>
                @else
                <h2>{{ __('poll.add') }}</h2>
                @endif
            </div>
        </div>
    </div>
 
 	<h3>
		<a href="{{ \URL::to('/'.$poll->parent_type.'/'.$poll->parent) }}">
			<em class="fas fa-hand-point-right"></em>
			@if ($poll->parent_type == 'teams')
			<em class="fas fa-user-friends"></em>
			@endif
			@if ($poll->parent_type == 'projects')
			<em class="fas fa-cogs"></em>
			@endif
			&nbsp;{{ $parent->name }}
		</a> 	
 	</h3>
 	@if ($poll->id > 0)
    <form action="{{ \URL::to('/polls/'.$poll->id) }}" method="post">
   @else
    <form action="{{ \URL::to('/polls') }}" method="post">
   @endif 
   @csrf
         <input type="hidden" name="id" value="{{ $poll->id }}" class="form-control" placeholder="">
         <input type="hidden" name="parent_type" value="{{ $poll->parent_type }}" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="{{ $poll->parent }}" class="form-control" placeholder="">
         <input type="hidden" name="status" value="{{ $poll->status }}" class="form-control" placeholder="">
         <input type="hidden" name="statuses" value="{{ $statuses }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('poll.status') }}:</label>
                    {{ __('poll.'.$poll->status) }}
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('poll.name') }}:</label>
                    <input type="text" name="name" value="{{ $poll->name }}"
                    	style="width:500px" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('poll.description') }}
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! \App\Models\Minimarkdown::stripLog($poll->description) !!}</textarea>
					<br />
					<label></label>Mini markdown szintaxis: kiemelt:**...**, dölt betü:*...*, kép: ![](url), link: http[s]://...., emojs: :), :(
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
		         	A választható alternatívákat tárolás után, a névre kattintva lehet megadni.
		        </div>
		    </div>     	
		 </div>         
         <div class="row">
         	<h3>{{ __('poll.config') }}</h3>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>{{ __('poll.pollType') }}:</label>
					<select name="pollType">
						<option	value="yesno"{{ selected('yesno',$poll->config->pollType) }}>
							{{ __('poll.yesno') }}
						</option>
						<option	value="onex"{{ selected('onex',$poll->config->pollType) }}>
							{{ __('poll.onex') }}
						</option>
						<option	value="morex"{{ selected('morex',$poll->config->pollType) }}>
							{{ __('poll.morex') }}
						</option>
						<option	value="pref"{{ selected('pref',$poll->config->pollType) }}>
							{{ __('poll.pref') }}
						</option>
					</select>				
                </div>
           </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>{{ __('poll.secret') }}:</label>
					<select name="secret">
						<option	value="1"{{ selected('1',$poll->config->secret) }}>
							{{ __('poll.secret') }}
						</option>
						<option	value="0"{{ selected('0',$poll->config->secret) }}>
							{{ __('poll.public') }}
						</option>
					</select>				
                </div>
           </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>{{ __('poll.liquied') }}:</label>
					<select name="liquied">
						<option	value="1"{{ selected('1',$poll->config->liquied) }}>
							{{ __('poll.yes') }}
						</option>
						<option	value="0"{{ selected('0',$poll->config->secret) }}>
							{{ __('poll.no') }}
						</option>
					</select>				
                </div>
           </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	A vita akkor indul meg ha a javaslatot a tagok
                	<input type="number" name="debateStart" min="0" max="100" 
                		value="{{ $poll->config->debateStart }}" 
                		style="sidth:50px" />%-a támogatja.
                </div>
           </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	Egy javasolt opció akkor kerül fel a "szavazó lapra" ha a javaslatot a tagok
                	<input type="number" name="optionActivate" min="0" max="100" 
                		value="{{ $poll->config->optionActivate }}" 
                		style="sidth:50px" />%-a támogatja.
                </div>
           </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>A vita időtartama:</label>
                	<input type="number" name="debateDays" min="0" max="100" 
                		value="{{ $poll->config->debateDays }}" 
                		style="sidth:50px" />nap
                </div>
           </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>A szavazás időtartama:</label>
                	<input type="number" name="voteDays" min="0" max="100" 
                		value="{{ $poll->config->voteDays }}" 
                		style="sidth:50px" />nap
                </div>
           </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                	<label>Érvényességi küszöb:</label>
                	<input type="number" name="valid" min="0" max="100" 
                		value="{{ $poll->config->valid }}" 
                		style="sidth:50px" />% -os részvétel a szavazáson
                </div>
           </div>
         </div>

	     <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('poll.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ \URL::previous() }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('poll.cancel') }}
	              </a>
	            </div>
	         </div>  
        </div>
   
    </form>
</x-guest-layout>  
