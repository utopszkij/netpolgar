<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$fileRec->name = \Request::old('name');
			$fileRec->description = \Request::old('description');
			$fileRec->type = \Request::old('type');
			$fileRec->licence = \Request::old('licence');
		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	@if ($fileRec->id > 0)
                <h2>{{ __('file.edit') }}</h2>
                @else
                <h2>{{ __('file.add') }}</h2>
                @endif
            </div>
        </div>
    </div>
 
    <div class="row">
    	<div class="col-12">
    		<a href="">
    			<em class="fas fa-hand-point-right"></em>{{ $parent->name}}
    		</a>
    	</div>
    </div>    
 
   @if ($fileRec->id > 0)
    <form action="{{ \URL::to('/file/update') }}" method="POST" enctype="multipart/form-data">
   @else
    <form action="{{ \URL::to('/file/store') }}" method="POST" enctype="multipart/form-data">
   @endif 
   @csrf
         <input type="hidden" name="id" value="{{ $fileRec->id }}" class="form-control" placeholder="">
         <input type="hidden" name="parent_type" value="{{ $fileRec->parent_type }}" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="{{ $fileRec->parent }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('file.name') }}:</label>
                    <input type="text" name="name" value="{{ $fileRec->name }}" 
                    	style="width:400px" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('file.description') }}
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! $fileRec->description !!}</textarea>
                </div>
            </div>
         </div>
         <input type="hidden" name="type" value="{{ $fileRec->type }}" class="form-control" placeholder="Tipus">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('file.licence') }}:</label>
                    <input type="text" name="licence" value="{{ $fileRec->licence }}" class="form-control" placeholder="Licensz">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>{{ __('file.upload') }}</label>
                    <input type="file" name="upload" value="" class="form-control" />
                    Max 2Mbyte, php, html, htm, js {{ __('file.disabled') }}.
                </div>    
			</div>
		</div>	
        <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('file.save') }}
	              </button>
	              <a class="btn btn-secondary" href="{{ \URL::previous() }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('file.cancel') }}
	              </a>
	            </div>
        </div>
   
    </form>
</x-guest-layout>  
