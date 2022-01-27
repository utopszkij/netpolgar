<x-guest-layout>  

	@if ($errors->any())
		@php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$product->name = \Request::old('name');
			$product->description = \Request::old('description');
			$product->avatar = \Request::old('avatar');
			$product->price = \Request::old('price');
			$product->currency = \Request::old('currency');
			$product->vat = \Request::old('vat');
			$product->stock = \Request::old('stock');
			$product->unit = \Request::old('unit');
			$product->status = \Request::old('status');
			$product->type = \Request::old('type');
		@endphp
	@endif
   <div id="product">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 @if ($product->id > 0)
                <h2>{{ __('product.edit') }}</h2>
                @else
                <h2>{{ __('product.add') }}</h2>
                @endif
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
					 style="width:15%; margin:10px; float:right" />
			 	@endif
			 	@if ($product->id > 0)
			    <form action="{{ \URL::to('/products/'.$product->id) }}" method="POST">
			   @else
			    <form action="{{ \URL::to('/products') }}" method="POST">
			   @endif 
			   @csrf
			        <input type="hidden" name="id" value="{{ $product->id }}" class="form-control" placeholder="">
			        <input type="hidden" name="parent_type" value="{{ $product->parent_type }}" class="form-control" placeholder="">
			        <input type="hidden" name="parent" value="{{ $product->parent }}" class="form-control" placeholder="">
			        <input type="hidden" name="stock" value="{{ $product->stock }}" class="form-control" placeholder="">
			        <input type="hidden" name="vat" value="{{ $product->vat }}" class="form-control" placeholder="">
			        <input type="hidden" name="type" value="{{ $product->type }}" class="form-control" placeholder="">
			        <input type="hidden" name="currency" value="{{ $product->currency }}" class="form-control" placeholder="">
			        <input type="hidden" id="categories" name="categories" value="{{ $categories }}" />
                <div class="form-group">
                    <label>{{ __('product.status') }}:</label>
                    <select name="status">
                    		@if($product->status == 'active')
                    			<option value="active" selected="selected">
										{{ __('product.active') }}                    			
                    			</option>
                    			<option value="inactive">
                    				{{ __('product.inactive') }}
                    			</option>
                    		@else
                    			<option value="active">
										{{ __('product.active') }}                    			
                    			</option>
                    			<option value="inactive" selected="selected">
                    				{{ __('product.inactive') }}
                    			</option>
                    		@endif
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('product.name') }}:</label>
                    <input type="text" name="name" value="{{ $product->name }}" 
                    class="form-control" placeholder="Név" style="width:600px">
                </div>
                <div class="form-group">
                    <label>{{ __('product.avatar') }}:</label>
                    <input type="text" name="avatar" value="{{ $product->avatar }}" 
                    class="form-control" placeholder="url" style="width:600px">
                    max 1M
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('product.description') }}:
                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás">{!! $product->description !!}</textarea>
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
                    {{ __('product.price') }}:
                    </label>
                    <input type="number" min="0" class="form-control"  
                    name="price" placeholder="Ár" value="{{ $product->price }}" />
                    NTC
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('product.stock') }}:
                    </label>
                    <input type="text" disabled="disabled" style="width:100px"  class="form-control" 
                    name="stock" value="{{ $product->stock }}" />
                    {{ __('product.unit') }}
                    <input type="text" style="width:80px"  class="form-control" 
                    name="unit" placeholder="db" value="{{ $product->unit }}" />
                </div>
					 <div class="form-group">
						<label>{{ __('product.addToStock') }}:</label>
						<input type="number" min="0" name="quantity" class="form-control" />
					</div>
	            <div>
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em>{{ __('product.save') }}
	              </button>
	              @if ($product->parent_type == 'teams')
	              <a class="btn btn-secondary" href="{{ \URL::to('/products/list/'.$product->parent) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('product.cancel') }}
	              </a>
	              @else
	              <a class="btn btn-secondary" href="{{ \URL::to('/products/list/0') }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('product.cancel') }}
	              </a>
	              @endif
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
