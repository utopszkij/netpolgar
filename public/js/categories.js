	 /*
	 * kategóriák, checkboxos fa struktúra megjelenitő (JQuery, jstree kell)
	 * szükséges JS elemek:  
	 *   treeData,  [{id, text, children:[...]},....]
	 *   categories = 'catId,catId...', 
	 *   doRefresh true|false
	 * szükséges DOM: #tree, #treeValue
	 */
	 var myTree = new Tree('#tree',{
		data: treeData,
		closeDepth: 2,
		onChange: treeChange
		
	 });

	 var changeTimer = false;
	 var firstChange = true;
	 var values = categories.split(',');
	 myTree.values = values;	 
	 	 
	 function treeChange() {
		if (changeTimer) {
			clearTimeout(changeTimer);		
		}
		$('#categories').val( myTree.values.toString() );
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
		$('#formRefresh').submit();
	 }

	 function treeIkonClick() {
		$('#tree').toggle();
		$('#treeValues').toggle();	 
	 }
