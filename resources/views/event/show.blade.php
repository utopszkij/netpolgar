<?php 
use App\Models\Minimarkdown;
?>

<x-guest-layout>  
	
   <div id="event">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('event.details') }}</h2>
            </div>
        </div>
    </div>
 
    <div class="row path" style="margin-top: 5px;">
    	<div class="col-12">
    	<h3>
			<a href="{{ \URL::to('/'.$event->parent_type.'/'.$event->parent) }}">
				<em class="fas fa-hand-point-right"></em>
				{{ $parent->name }}
			</a>
		</h3>	
		</div>
	 </div>    
     <div class="row path" style="margin-top: 5px;">
    	<div class="col-12">
    	<h2>
            {{ $event->name }} 
            @if ($userAdmin)
            <a href="{{ \URL::to('/events/'.$event->id.'/edit') }}">
            	<em class="fas fa-edit" title="módosítás"></em>
            </a>&nbsp;
            <a href="{{ \URL::to('/events/'.$event->id.'/delete') }}">
            	<em class="fas fa-eraser" title="törlés"></em>
            </a>&nbsp;
             
            @endif
		</h2>	
		</div>
	 </div>    
	 
	 <div class="row">
			<div class="col-12">
			   @if ($event->avatar != "") 
					 <img src="{{ $event->avatar}}"
					 style="width:15%; margin:10px; float:right" />
			   @endif
			   <div class="form-group">
		        	<label>ID:</label>{{ $event->id }}
		        </div>
                <div class="form-group">
                    <label>{{ __('event.avatar') }} (max 2M):</label>
                    {{ $event->avatar }} 
				</div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('event.description') }}:
                    </label>
                    {!! Minimarkdown::miniMarkdown($event->description)  !!}
                    
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('event.date') }}:
                    </label>
                    {{ $event->date }} {{ $event->hours }}:{{ $event->minutes }}
	            <div>
			    <div class="form-group">
					<label>{{ __('event.length') }}:</label>
					{{ $event->length }} 
					</div>
	            <div>
			    <div class="form-group">
					<label>{{ __('event.location') }}:</label>
					{{ $event->location }}
				</div>

                <div class="form-group">
                	<label></label>
					<a class="" 
	        			   href="{{ \URL::to('/like/events/'.$event->id) }}" 
	        			   title="részt veszek">
	        				@if ($info->userLiked)
	        				<em class="fas fa-check"></em>
	        				@endif
	        				<em class="fas fa-thumbs-up"></em>
	        				<a href="{{ \URL::to('/likeinfo/events/'.$event->id) }}">
		        				({{ $info->likeCount }})
		        			</a>	
							{{ __('event.like') }}
	        		</a>             
	        		&nbsp;&nbsp;&nbsp;
					<a class="" 
	        			   href="{{ \URL::to('/message/tree/events/'.$event->id) }}" 
	        			   title="{{ __('event.comments') }}">
	        				<em class="fas fa-comments"></em>
	        				({{ $info->commentCount }})
							{{ __('event.comments') }}
	        		</a>             
				</div>
					                
    	        <div class="row">
    	        	<div class="col-12">
    	              <a class="btn btn-secondary" href="{{ \URL::previous() }}">
    	                  <em class="fas fa-ban"></em>
    	                  {{ __('event.back') }}
    	              </a>
    	            </div>  
    	        </div>

			</div> <!-- body -->		
	 	</div> <!-- tree - body -->
 	</div>
    
</x-guest-layout>  
