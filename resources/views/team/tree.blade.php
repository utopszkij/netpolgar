<x-guest-layout>  
	<div id="teamContainer" class="row">
		<div class="col-12">
			<p>&nbsp;</p>
			<h2>{{ __('team.teams') }}</h2>
			<div id="tree">
				tree values
			</div>
			<p>&nbsp;</p>
		</div>
    </div>   
   	 <script src="/js/tree.js"></script>
	 <script type="text/javascript">
	 var data = {!! $data !!};
	 console.log(data);
	 var myTree = new Tree('#tree',{
		"data": data,
		"onChange": treeChange
	 });
	 $('.treejs-checkbox').hide();
	 
	 function treeChange() {
		 if (myTree.clickedId > 0) {
			window.location = "{{ \URL::to('/teams/') }}/"+myTree.clickedId;
		 }	
	 }
	 </script>
     
</x-guest-layout>  
