<x-guest-layout>  
{{--
	crf token, pollId
	A további Küldött adatok pollType -től függően külömnözőek:
   
	yesno: vote = 1 | 0 
	onex:  vote = $poll->id
	morex: vote{n} = $poll->id   ahol n=0,1,2...20 nem mind
	pref:  opt_{n} = $poll->id    ahol n=0,1,2...20 nem mind
			 pos_{n} = position 	
 --}}
	<div id="voteForm">
	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	<h2>
            		</h2><em class="fas fa-balance-scale"></em>
                	{{ __('poll.vote') }}
               </h2>
            </div>
        </div>
    </div>
 
	<h3>
		<a href="{{ \URL::to('/'.$poll->parent_type.'/'.$parent->id) }}">
			<em class="fas fa-hand-point-right"></em>
			{{ $parent->name }}
		</a>
	</h3>
	<h4>
		<a href="{{ \URL::to('/polls/'.$poll->id) }}">
			<em class="fas fa-hand-point-right"></em>
			{{ $poll->name }}
		</a>	
	</h4>
   <form id="formVote" action="{{ \URL::to('/votes') }}" method="post">
   @csrf
      <input type="hidden" name="pollId" value="{{ $poll->id }}" />
      
		@if ($poll->config->pollType == 'yesno')
	    <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 text-center">
				<ul>
				@foreach ($options as $option)
				<li>
					<input type="radio" name="vote" value="{{ $option->id }}" checked="checked" />
					{{ $option->name }}
				</li>
				@endforeach
				</ul>
				<br />
			</div>	
		</div>	
		@endif

		@if ($poll->config->pollType == 'onex')
	    <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 text-center">
				<ul>
				@foreach ($options as $option)
					<li>
						<input type="radio" name="vote" value="{{ $option->id }}" checked="checked" />
						{{ $option->name }}
					</li>
				@endforeach
				</ul>
				<br />
			</div>
		</div>	
		@endif

		@if ($poll->config->pollType == 'morex') 
		<div class="row">
        	<div class="col-xs-12 col-sm-12 col-md-12 text-center help">
				Több is bejelölhető
			</div>	
		</div>	
		 <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 text-center">
         <ul>
         @foreach ($options as $p => $option)
         <li>
         	<input type="checkbox" name="vote{{ $p }}" value="{{ $option->id }}" />
         	{{ $option->name }}
         </li>
         @endforeach
         </ul>
         <br />
			</div>
		</div>	
		@endif

		@if ($poll->config->pollType == 'pref') 
	   <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 text-center help">
         A <em class="fas fa-angle-up"></em> /
         <em class="fas fa-angle-down"></em> gombokkal rendezd sorrendbe a
         lehetőségeket! Legfelül legyen amit a legjobbnak tartasz. Legalul amit
         a legrosszabbnak.
         A <em class="fas fa-arrows-alt-v"></em> -ra kattintva a felette lévővel
         azonos pozicióba sorolod a lehetőséget, 
         ugyanerre ismét kattintva visszavonod az azonos rangsorolást.
         </div>
      </div>
	   <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 text-center">
         <table class="voteTable" style="display:inline-block; width:auto">
         @foreach ($options as $p => $option)
         <tr id="tr_{{ $p }}" class="">
         	<td id="td0_{{ $p }}" style="width:40px">
         	@if ($p > 0)
         	<var onclick="upClick({{ $p }})">
         		<em class="fas fa-angle-up"></em>
         	</var>
         	@endif
         	</td>
         	<td id="td1_{{ $p }}">
         	<span id="name_{{ $p }}">{{ $option->name }}</span>
         	<input type="hidden" id="opt_{{ $p }}" name="opt_{{ $p }}" value="{{ $option->id }}" />
         	<input type="hidden" id="pos_{{ $p }}" name="pos_{{ $p }}" value="{{ $p }}" />
         	</td>
         	<td id="td2_{{ $p }}" style="width:40px">
         	@if ($p < (count($options) - 1))
         	<var onclick="downClick({{ $p }})">
         		<em class="fas fa-angle-down"></em>
         	</var>
         	@endif
         	</td>
         	<td id="td3_{{ $p }}" style="width:40px">
         	@if ($p > 0)
         	<var onclick="eqClick({{ $p }})">
         		<em class="fas fa-arrows-alt-v"></em>
         	</var>
         	@endif
         	</td>
         </tr>
         @endforeach
         </table>
         <br />
			</div>
		</div>	
		@endif

	   <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="button" class="btn btn-primary"
	              	onclick="saveClick()">
	              		<em class="fas fa-check"></em>{{ __('poll.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ \URL::previous() }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('poll.cancel') }}
	              </a>
	            </div>
	   </div>  
    </form>
    </div>
    
    <script type="text/javascript">
    
    	$('.voteTable var').attr('style','cursor:pointer');
    	
		function upClick(p) {
			// cserél p <-> p-1
			var optName = $('#name_'+(p-1)).html();
			var optId = $('#opt_'+(p-1)).val();
			$('#name_'+(p-1)).html( $('#name_'+p).html() );
			$('#opt_'+(p-1)).val( $('#opt_'+p).val() );
			$('#name_'+p).html( optName );
			$('#opt_'+p).val( optId );
			return false;
		}

		function downClick(p) {
			// cserél p <-> p+1
			var optName = $('#name_'+(p+1)).html();
			var optId = $('#opt_'+(p+1)).val();
			$('#name_'+(p+1)).html( $('#name_'+p).html() );
			$('#opt_'+(p+1)).val( $('#opt_'+p).val() );
			$('#name_'+p).html( optName );
			$('#opt_'+p).val( optId );
			return false;
		}

		function eqClick(p) {
			var s = $('#tr_'+p).attr('class');
			if (s == '') {
				$('#tr_'+p).attr('class','eq');
			} else {
				$('#tr_'+p).attr('class','');
			}
		} 
		
		function saveClick() {
			var p = 1;
			while (p < 20) {
				if ($('#tr_'+p).attr('class') == 'eq') {
					$('#pos'+p).val( $('#pos'+(p-1)).val );				
				}
				p++;			
			}
			$('#formVote').submit();
		}   
    </script>
</x-guest-layout>  
