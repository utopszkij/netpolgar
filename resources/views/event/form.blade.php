<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$event->id = \Request::old('id');
			$event->name = \Request::old('name');
			$event->description = \Request::old('description');
			$event->avatar = \Request::old('avatar');
			$event->date = \Request::old('date');
			$event->hours = \Request::old('hours');
			$event->minutes = \Request::old('minutes');
			$event->length = \Request::old('length');
			$event->location = \Request::old('location');
		@endphp
	@endif
   <div id="event">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($event->id > 0)
                <h2>{{ __('event.edit') }}</h2>
                @else
                <h2>{{ __('event.add') }}</h2>
                @endif
            </div>
        </div>
    </div>
 
    <div class="row path" style="margin-top: 5px;">
    	<div class="col-12">
    	<h3>
    		@if ($parent)
			<a href="{{ \URL::to('/'.$parentType.'/'.$parent->id) }}">
				<em class="fas fa-hand-point-right"></em>
				{{ $parent->name }}
			</a>
			@endif
		</h3>	
		</div>
	 </div>    
	 <div class="row">
			<div class="col-12">
			   @if ($event->avatar != "") 
					 <img src="{{ $event->avatar}}"
					 style="width:15%; margin:10px; float:right" />
			   @endif
			   @if ($event->id > 0)
			    <form action="{{ \URL::to('/events/'.$event->id) }}" method="POST" enctype="multipart/form-data">
			   @else
			    <form action="{{ \URL::to('/events') }}" method="POST" enctype="multipart/form-data">
			   @endif 
			   @csrf
		        <input type="hidden" name="id" value="{{ $event->id }}" class="form-control" placeholder="">
		        <input type="hidden" name="parent_type" value="{{ $event->parent_type }}" class="form-control" placeholder="">
		        <input type="hidden" name="parent" value="{{ $event->parent }}" class="form-control" placeholder="">
                <div class="form-group">
                    <label>{{ __('event.name') }}:</label>
                    <input type="text" name="name" value="{{ $event->name }}" 
                    class="form-control" placeholder="Név" style="width:600px">
                </div>
                <div class="form-group">
                    <label>{{ __('event.avatar') }} (max 2M):</label>
                    <input type="text" name="avatar" value="{{ $event->avatar }}" 
                    class="form-control" placeholder="url" style="width:600px">
				</div>
                <div class="form-group">
                    <label></label>{{ __('event.orUpload') }}<br />
                    <label></label>
                    <input type="file" name="img" value="" class="form-control" />
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('event.description') }}:
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! $event->description !!}</textarea>
	                 	<p>használható korlátozott "markdown" szintaxis.
								kiemelt: <strong>**...**</strong>,
								dölt betüs: <strong>*...*</strong> ,
								kép: <strong>![](http...)</strong>, 
								link: <strong>http....</strong>
								:(,   :),  :|<br />
								max. 3 kép lehet, max. képfile méret: 2M
							</p>

                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('event.date') }}:
                    </label>
                    <input type="date" class="form-control"  
                    name="date" placeholder="Dátum" value="{{ $event->date }}" />
					<var>{{ __('event.hours') }}:</var>
					<input type="number" min="0" max="24" name="hours" 
					value="{{ $event->hours }}" class="form-control" />
					<var>{{ __('event.minutes') }}:</var>
					<input type="number" min="0" max="59" name="minutes" 
					value="{{ $event->minutes}}" class="form-control" />
	            <div>
			    <div class="form-group">
					<label>{{ __('event.length') }}:</label>
					<input type="text" name="length" placeholder="pl. 30 perc" 
					value="{{ $event->length }}" class="form-control" /> 
					</div>
	            <div>
			    <div class="form-group">
					<label>{{ __('event.location') }}:</label>
					<input type="text" size="80" name="location" 
					value="{{ $event->location }}" class="form-control" />
					</div>
	            <div>
	            
	              <button type="submit" class="btn btn-primary" onclick="true">
	              		<em class="fas fa-check"></em>{{ __('event.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ \URL::to('/'.$parentType.'/'.$event->parent) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('event.cancel') }}
	              </a>
	            </div>
			    </form>

			</div> <!-- body -->		
	 	</div> <!-- tree - body -->
 	</div>
    
</x-guest-layout>  
