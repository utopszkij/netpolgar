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
		<div class="row">
			<div class="col-12">
				<var><big><strong>{{ __('product.title') }}</strong></big></var>
				<var style="float:right">
					<a class="btn btn-success">
						<em class="fas fa-shopping-basket"></em>
						&nbsp;{{ __('product.basket') }}
					</a>
					&nbsp;&nbsp;				
				</var>			
			</div>		
		</div>
		<div class="row">
			<div class="col-12 col-md-3">
				<div id="submenu">
					@if ($team)
					<div>
						{{ $team->name }} 
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
					@if ($userAdmin)
					<a href="{{ \URL::to('/products/create/'.$team->id) }}" class="btn btn-primary">
						<em class="fas fa-plus"></em>
						&nbsp;{{ __('product.add') }}					
					</a>
					@endif
				</div>
				<div class="col-12 col-md-9">
					{{ __('product.search') }}: <input type="text" id="search" name="search"
								 value="{{ $search}}" />
					<button class="btn btn-secondary">
						<em class="fa fa-search"></em>				
					</button>
					<button type="button" class="btn btn-secondary"
						onclick="$('#search').val(''); $('#formRefresh').submit();">
						<em class="fa fa-times"></em>				
					</button>
				</div>
				</form>
				@if (count($data) > 0)
				@foreach ($data as $product)
				<div class="productsItem {{ $product->status }}">
					<h3>{{ $product->name }}
						@if ($userAdmin)
						<a href="">
							<em class="fas fa-edit"></em>					
						</a>
						@endif
					</h3>
					<p class="imgContainer"> 
						<img src="{{ $product->avatar }}" />
					</p>	
					<p>{{ __('product.price') }}:
					<strong>{{ $product->price }}</strong>
					&nbsp;&nbsp;&nbsp;
					{{ __('product.stock') }}:
					{{ $product->stock }}
					&nbsp;{{ $product->unit}}</p>
					<div>{!!  evaluation($product->value) !!}					
					</div>
					<p>
						<input type="number" value="1" class="quantity" />
						<a class="btn btn-primary">
							<em class="fas fa-caret-right"></em>						
							<em class="fas fa-shopping-basket"></em>						
							{{ __('product.addToBasket') }}
						</a>
					</p>										
				</div>
				@endforeach
				@else 
					{{ __('product.notRecord') }}
				@endif
			</div>
		</div>
   	 @if (count($data) > 0)
	   	 {!! $data->links('pagination') !!}
   	 @endif
	 </div> 
	 
	 <script src="/js/tree.js"></script>
	 <script>
	 
	 var treeData =
	 @php  include(storage_path().'/categories.json') @endphp
	 ;
	 var myTree = new Tree('#tree',{
		data: treeData,
		closeDepth: 2,
		onChange: treeChange
		
	 });

	 var changeTimer = false;
	 var firstChange = true;
	 var categories = "{{ $categories }}";
	 var values = categories.split(',');
	 myTree.values = values;	 
	 	 
	 function treeChange() {
		if (changeTimer) {
			clearTimeout(changeTimer);		
		}
		$('#categories').val( myTree.values.toString() );
		console.log('treeChanged');
		console.log($('#categories').val());
		console.log(myTree.selectedNodes);
		if (!firstChange) {
			changeTimer = window.setTimeout(refresh,1000);
		}	
		
		var s = '';
		for (var i = 0; i < myTree.selectedNodes.length; i++) {
			var node = myTree.selectedNodes[i];
			if (node.status == 2) {
				s += '- '+node.text+'<br />';
			}
		}
		$('#treeValues').html(s);
		firstChange = false; 	 
	 }
	 
	 function refresh() {
		console.log('refresh');	 
		$('#formRefresh').submit();
	 }
	 
	 function treeIkonClick() {
		$('#tree').toggle();
		$('#treeValues').toggle();	 
	 }
	 </script>       
</x-guest-layout>  
