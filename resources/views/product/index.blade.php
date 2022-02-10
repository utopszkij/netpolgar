<x-guest-layout>  

	@php
		function evaluation($value) {
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
			$result .= '</div>';
			return $result;		
		}
		
		function selected($a,$b) {
			if ($a == $b) {
				$result = ' selected="selected"';
			} else {
				$result = '';
			}
			return $result;	
		}
	@endphp

	<div id="products">
		@if (\Auth::user())
		<div class="row">
			<div class="col-12">
				<br />
				<var><big><strong>{{ __('product.title') }}</strong></big></var>
				<var style="float:right">
					<a class="btn btn-success" href="{{ \URL::to('/carts/list') }}">
						<em class="fas fa-shopping-basket"></em>
						&nbsp;{{ __('product.basket') }}
					</a>
					&nbsp;&nbsp;				
				</var>			
			</div>		
		</div>
		@else
		<div class="row">
			<div class="col-12">
				&nbsp;
			</div>
		</div>			
		@endif
		
		<div class="row">
			<div class="col-12 col-md-3">
				<div id="submenu">
					<h4>{{ __('product.filter') }}</h4>
					@if ($team)
					<div>
						{{ $team->name }} 
						<a href="{{ \URL::to('/products/list/0') }}" 
							class="btn btn-secondary">X</a>				
					</div>
					@endif
					@if ($user)
					<div>
						{{ $user->name }} 
						<a href="{{ \URL::to('/products/list/0') }}" 
							class="btn btn-secondary">X</a>				
					</div>
					@endif
					
					<div id="treeIkon" onclick="treeIkonClick()" style="cursor:pointer">
						<em class="fas fa-bars"></em>
					</div>
					<div id="treeValues">
						tree values
					</div>
					<div id="tree">
										
					</div>		
				</div>						
			</div>
			<div class="col-12 col-md-9">
				<form method="get" id="formRefresh" 
					action="{{ \URL::to('/products/list/'.$teamId) }}">
				<input type="hidden" id="categories" name="categories"
					value="{{ $categories}}" />
				<div>
					{{ __('product.sort') }}:
					<select name="order" onchange="$('#formRefresh').submit()">
						<option value="name,asc"{{ selected('name,asc',$order) }}>ABC</option>					
						<option value="price,asc"{{ selected('price,asc',$order) }}>{{ __('product.priceASC') }}</option>					
						<option value="price,desc"{{ selected('price,desc',$order) }}>{{ __('product.priceDESC') }}</option>					
						<option value="value,desc"{{ selected('value,desc',$order) }}>{{ __('product.evaluations') }}</option>					
					</select>
					@if (\Auth::user())					
						@if ($team)
						<a href="{{ \URL::to('/products/create/'.$team->id) }}" class="btn btn-primary">
							<em class="fas fa-plus"></em>
							&nbsp;{{ __('product.add') }}					
						</a>
						@else
						<a href="{{ \URL::to('/products/create/0') }}" class="btn btn-primary">
							<em class="fas fa-plus"></em>
							&nbsp;{{ __('product.add') }}					
						</a>
						@endif
					@endif
					<div style="display:inline-block; width:auto">					
						{{ __('product.search') }}: <input type="text" id="search" name="search"
									 value="{{ $search}}" />
						<button class="btn btn-secondary">
							<em class="fa fa-search"></em>				
						</button>
					</div>
					<button type="button" class="btn btn-secondary"
						onclick="$('#search').val(''); $('#formRefresh').submit();">
						<em class="fa fa-times"></em>				
					</button>
				</div>
				</form>
				@if ($data->total > 0)
				@foreach ($data->items as $key => $product)
					@if (($key >= $data->offset) &
					     ($key < ($data->offset + $data->perPage)))				
						<div class="productsItem {{ $product->status }}">
							<div class="productItemBody">
    							<h3><a href="{{ \URL::to('/products/'.$product->id) }}">
    									{{ $product->name }}
    								 </a>
    								@if ($product->userAdmin)
    								<a href="{{ \URL::to('/products/'.$product->id.'/edit') }}">
    									<em class="fas fa-edit"></em>					
    								</a>
    								@endif
    							</h3>
    							<a href="{{ \URL::to('/products/'.$product->id) }}">
    								<p class="imgContainer"> 
    									<img src="{{ $product->avatar }}" />
    								</p>	
    							</a>
							</div>
							<p>{{ __('product.price') }}:
    								<strong>{{ $product->price }}</strong>
    								&nbsp;&nbsp;&nbsp;
    								{{ __('product.stock') }}:
    								{{ $product->stock }}
    								&nbsp;{{ $product->unit}}
    						</p>
							<div>{!!  evaluation($product->value) !!}					
							</div>
							<p>
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
							</p>
						</div>
					@endif	
				@endforeach
				@else 
					{{ __('product.notRecord') }}
				@endif
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<nav>
		         <ul class="pagination pull-right">
		    	 	@php
	    	 		$p = 1;
		    	 	if ($data->currentPage > 1) {
	    	 			$url = \URL::to('/products/list/'.$teamId).'?page=0';
               	echo '<li class="page-item" title="első lap">
                    <a class="page-link" 
                    href="'.$url.'">&lsaquo;&lsaquo;</a></li>';
		    	 	}
		    	 	if ($data->total > $data->perPage) {
		    	 		$offset = 0;
		    	 		$p = 1;
		    	 		while ($offset < $data->total) {
		    	 			if (($p > ($data->currentPage - 3)) &
		    	 			    ($p < ($data->currentPage + 3))) {
			    	 			$url = \URL::to('/products/list/'.$teamId).'?page='.$p;
			    	 			if ($p == $data->currentPage) {
									echo '<li class="page-item active">
											<span class="page-link">'.$p.'</span></li>';
			    	 			} else {
									echo '<li class="page-item">
											<a class="page-link" href="'.$url.'">
											'.$p.'</a></li>';
								}			
							}
							$p++;
							$offset = $offset + $data->perPage;    	 		
		    	 		}
		    	 	}
		    	 	if ($data->currentPage < ($p - 1)) {
	    	 			$url = \URL::to('/products/list/'.$teamId).'?page='.($p-1);
               	echo '<li class="page-item" title="első lap">
                    <a class="page-link" 
                    href="'.$url.'">&rsaquo;&rsaquo;</a></li>';
		    	 	}
			  	 	@endphp
			  	 	</ul>
		  	 	</nav>
	  	 	</div>
	  	 </div>
	 </div> 
	 
	 <script src="/js/tree.js"></script>
	 <script type="text/javascript">
	 var treeData =
	 @php  include(storage_path().'/categories.json') @endphp
	 ;
	 var categories = "{{ $categories }}";
	 var doRefresh = true;
	 </script>
	 <script src="/js/categories.js"></script>
	 	
</x-guest-layout>  
