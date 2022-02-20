<x-guest-layout>  

@php
		function evaluation($value,$userUsed, $fileRec) {
			$result = '<div class="evaluation">';
			if ($value > 4.5) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>';
			} else if ($value > 4.25) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star-half-alt"></em>';
			} else if ($value > 3.75) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<img src="/img/star.png" />';
			} else if ($value > 3.25) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star-half-alt"></em>
				<img src="/img/star.png" />';
			} else if ($value > 2.75) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			} else if ($value > 2.25) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<em class="fas fa-star-half-alt"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			} else if ($value > 1.75) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			} else if ($value > 1.25) {
				$result .= '<em class="fas fa-star"></em>
				<em class="fas fa-star-half-alt"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			} else if ($value > 0.75) {
				$result .= '<em class="fas fa-star"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />					
				<img src="/img/star.png" />';
			} else if ($value > 0.25) {
				$result .= '<em class="fas fa-star-half-alt"></em>
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			} else {
				$result .= '<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />
				<img src="/img/star.png" />';
			}
			if ($userUsed) {
				$result .= '<a href="'.\URL::to('/files/evaluation/'.$fileRec->id).'">
							<em class="fas fa-arrows-alt-v"></em>Értékelem
						</a>';
            }
			$result .= '</div>';
			return $result;		
		}

		$file->description = str_replace("\n",'<br />',$file->description);
		$file->description = strip_tags($file->description,['br']);
	@endphp

	<div id="fileContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
				<br />
                <big>{{ __('file.details') }}</big>
            </div>
        </div>
    </div>
	<div class="row">
       <div class="col-12">
       		<a href="{{ \URL::to('/'.$file->parent_type.'/'.$file->parent) }}">
       			<em class="fas fa-hand-point-right"></em>
       			{{ $parent->name }}
       		</a>
	  </div>
	</div>	    
	<div class="row">
       <div class="col-12">
             <h3>
             	{{ $file->name }}
		        @if ($info->userAdmin) 
	            &nbsp;<a href="{{ \URL::to('/files/'.$file->id.'/edit') }} ">
						<em class="fas fa-edit" title="{{ __('file.edit') }}"></em></a>
	            &nbsp;<a href="#" onclick="delClick()">
						<em class="fas fa-eraser" title="{{ __('file.delete') }}"></em></a>
						                
   	            @endif
             </h3>
         </div>
	</div>
	<div class="row">
       <div class="col-12">
       		<p>{{ __('file.description') }}</p>
       		<p>{{ str_replace("\n",'<br />',$file->description) }}</p>
       </div>
    </div>   
	<div class="row">
       <div class="col-12">
       		<p>{{ __('file.type') }}: {{ $file->type }}</p>
       </div>
    </div>   
	<div class="row">
       <div class="col-12">
       		<p>{{ __('file.licence') }}: {{ $file->licence }}</p>
       </div>
    </div>   
	<div class="row">
       <div class="col-12">
       		<p>{{ __('file.fileSize') }}: {{ $info->fileSize }} Byte</p>
       </div>
    </div>   
	<div class="row">
       <div class="col-12">
       		<p>{{ __('file.downloadCount') }}: {{ $info->downloadCount }}</p>
       </div>
    </div>   
	<div class="row">
       <div class="col-12">                    <br />
       	<a 
			   href="{{ \URL::to('/like/files/'.$file->id) }}" 
			   title="Tetszik">
				@if ($info->userLiked)
				<em class="fas fa-check"></em>
				@endif
				<em class="fas fa-thumbs-up"></em>
				<a href="{{ \URL::to('/likeinfo/files/'.$file->id) }}">
    				{{ $info->likeCount }}
				</a>
				{{ __('file.like') }}
			</a>
			<a 
			   href="{{ \URL::to('/dislike/files/'.$file->id) }}" 
			   title="Nem tetszik">
				@if ($info->userDisLiked)
				<em class="fas fa-check"></em>
				@endif
				<em class="fas fa-thumbs-down"></em>
				<a href="{{ \URL::to('/likeinfo/files/'.$file->id) }}">
    				{{ $info->disLikeCount }}
				</a>
				{{ __('file.dislike') }}
			</a>
	   </div>
	   <div class="row">
	   		<div class="col-12">
	   		<br />
	        	{!! evaluation($info->evaulation, $info->userUsed, $file) !!}
	        <br />	
	        </div>	
	   </div>
    </div>   
	<div class="row">
       <div class="col-12">
       		<a href="{{ \URL::to('files/'.$file->id.'/download') }}" class="btn btn-primary"
       			onclick="false;" target="_dowload">
	       		<em class="fas fa-cloud-download-alt"></em>
       			{{ __('file.download') }}
       		</a>
       		<a href="{{ \URL::previous() }}" class="btn btn-secondary">
	       		<em class="fas fa-reply"></em>
       			{{ __('file.back') }}
       		</a>
       </div>
    </div>   
   </div>
   
   <script type="text/javascript">
   		function delClick() {
		   popupConfirm("{{ __('file.sureDelete') }}", 
		   	function() {
		   		$('#waiting').show();
		   		location="{{ \URL::to('/files/'.$file->id.'/delete') }}";
		   	}, true);
   		  return false;	
   		}
   </script>
	
</x-guest-layout>  
