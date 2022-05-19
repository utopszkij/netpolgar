<x-guest-layout>  

   <div id="product">
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
			<a href="{{ \URL::to('/teams/'.$team->id) }}">
				<em class="fas fa-hand-point-right"></em>
				<em class="fas fa-users"></em>
				{{ $team->name }}
			</a>
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
						  {{ __('product.'.$roduct->status) }}                    			
                </div>
                <div class="form-group">
                    <label>{{ __('product.name') }}:</label>
                    {{ $product->name }}
                </div>
                <div class="form-group">
                    <label style="vertical-align: top;">
                    {{ __('product.description') }}:
                    </label>
                    {{ $product->description }}
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
                    {{ $product->stock }}  {{ __('product.unit') }}
                </div>
	            <div>
	              <a class="btn btn-secondary" href="{{ \URL::to('/products/list/'.$team->id) }}">
	                  <em class="fas fa-ban"></em>
	                  {{ __('product.OK') }}
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
