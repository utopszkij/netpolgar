<?php
use App\Models\Minimarkdown;
?>
<x-guest-layout>  

	@php
		function evaluation($value,$userUsed, $product) {
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
				$result .= '<a href="'.\URL::to('/products/evaluation/'.$product->id).'">
							<em class="fas fa-arrows-alt-v"></em>Értékelem
						</a>';
            }
			$result .= '</div>';
			return $result;		
		}

		$product->description = str_replace("\n",'<br />',$product->description);
		$product->description = strip_tags($product->description,['br']);
	@endphp


   <div id="product">
		@if (\Auth::user())
		<div class="row">
				<div class="col-12" style="text-align:right;">
						<a class="btn btn-success" href="{{ \URL::to('/carts/list') }}">
							<em class="fas fa-shopping-basket"></em>
							&nbsp;{{ __('product.basket') }}
						</a>
				</div>		
		</div>
		@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ __('product.details') }}</h2>
            </div>
        </div>
    </div>
 
    <div class="row path" style="margin-top: 5px;">
    	<div class="col-12">
    	<h3>
    		@if ($team)
			<a href="{{ \URL::to('/teams/'.$team->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-users"></em>
				{{ $team->name }}
			</a>
			@else
				{{ $parentUser->name }}
			@endif
		</h3>	
		</div>
	 </div>    
	 <div class="row">
			<div class="col-12 col-md-3">
				<div id="submenu">
					<div id="treeIkon" onclick="treeIkonClick()" style="cursor:pointer">
						<em class="fas fa-bars"></em>
					</div>
					<div id="treeValues"></div>
					<div id="tree"></div>		
				</div>						
			</div> 
			<div class="col-12 col-md-9">
			 	@if ($product->avatar != "") 
					 <img src="{{ $product->avatar}}"
					 style="width:35%; margin:10px; />
			 	@endif
		      <form action="{{ \URL::to('/products/list') }}" method="GET">
			   @csrf
			        <input type="hidden" name="id" value="{{ $product->id }}" class="form-control" placeholder="">
			        <input type="hidden" name="team_id" value="{{ $product->team_id }}" class="form-control" placeholder="">
			        <input type="hidden" name="stock" value="{{ $product->stock }}" class="form-control" placeholder="">
			        <input type="hidden" name="vat" value="{{ $product->vat }}" class="form-control" placeholder="">
			        <input type="hidden" name="type" value="{{ $product->type }}" class="form-control" placeholder="">
			        <input type="hidden" name="currency" value="{{ $product->currency }}" class="form-control" placeholder="">
			        <input type="hidden" id="categories" name="categories" value="{{ $categories }}" />
                <div class="form-group">
                    <label>{{ __('product.status') }}:</label>
						  {{ __('product.'.$product->status) }}                    			
                </div>
                <div class="form-group">
                    <h3>{{ $product->name }}</h3>
                </div>
                <div class="form-group">
                    {!! Minimarkdown::miniMarkdown($product->description)  !!}
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('product.price') }}:
                    </label>
                    {{ $product->price }} {{ $product->currency }}
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('product.stock') }}:
                    </label>
                    {{ $product->stock }}  {{ $product->unit }}
                </div>
                <div class="form-group">
                	{!! evaluation($info->evaulation, $info->userUsed, $product) !!}
					 </div>   
                <div class="form-group">
                	<form action="/carts/add" method="get">
                	   <input type="hidden" 
                	   	name="product_id" value="{{ $product->id }}" />
               		<input name="quantity" type="number" value="1" class="quantity" />
							<button type="submit" class="btn btn-primary">
								<em class="fas fa-caret-right"></em>						
								<em class="fas fa-shopping-basket"></em>						
								{{ __('product.addToBasket') }}
							</button>
						</form>	
					 </div>   
                <div class="form-group">
						<a class="" 
	        			   href="{{ \URL::to('/like/products/'.$product->id) }}" 
	        			   title="tetszik">
	        				@if ($info->userLiked)
	        				<em class="fas fa-check"></em>
	        				@endif
	        				<em class="fas fa-thumbs-up"></em>
	        				<a href="{{ \URL::to('/likeinfo/products/'.$product->id) }}">
		        				({{ $info->likeCount }})
		        			</a>	
							{{ __('product.like') }}
	        			</a>             
	        			&nbsp;&nbsp;&nbsp;
						<a class="" 
	        			   href="{{ \URL::to('/dislike/products/'.$product->id) }}" 
	        			   title="nem tetszik">
	        				@if ($info->userDisLiked)
	        				<em class="fas fa-check"></em>
	        				@endif
	        				<em class="fas fa-thumbs-up"></em>
	        				<a href="{{ \URL::to('/likeinfo/products/'.$product->id) }}">
		        				({{ $info->disLikeCount }})
		        			</a>	
							{{ __('product.dislike') }}
	        			</a>             
	        			&nbsp;&nbsp;&nbsp;
						<a class="" 
	        			   href="{{ \URL::to('/message/tree/products/'.$product->id) }}" 
	        			   title="{{ __('products.comments') }}">
	        				<em class="fas fa-comments"></em>
	        				({{ $info->commentCount }})
							{{ __('product.comments') }}
	        			</a>             
	        			&nbsp;&nbsp;&nbsp;
						<a href="{{ \URL::to('/order/listbyproduct/'.$product->id) }}">
							<em class="fas fa-truck"></em>					
							{{ __('product.stockEvents') }}
						</a>
	        			   
					</div>                
	            <div>
	              <a class="btn btn-secondary" href="{{ \Request::session()->get('productsListUrl') }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('product.back') }}
	              </a>
	            </div>
			    </form>

			</div> <!-- body -->		
	 	</div> <!-- tree - body -->
 	</div>
 
	 <script src="/js/tree.js"></script>
	 <script type="text/javascript">
	 var treeData =
	 @php  include(storage_path().'/categories.json') @endphp
	 ;
	 var doRefresh = false;
	 var categories = "{{ $categories }}";
	 </script>
	 <script src="/js/categories.js"></script>
	 
 
    
</x-guest-layout>  
