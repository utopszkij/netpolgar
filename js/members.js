  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !
  function pageOnLoad() {
  }	

  function membersFun() {
	  if ('#browseForm'.length) {
		  $scope.trClass = 'tr1';
		  $scope.filterStateSelected = function(s,filterstate) {
		  	//result: '' or ' "selected"="selected"
			var result = '';
			if (s == filterstate) {
				result = ' selected="selected"';
			}
			return result;  
		  }
		  $scope.thClass = function(s, order, order_dir)  {
		  	//result 'unorder' or 'order'
		  	var result = 'unorder';
		  	if (s == order) {
		  		result = 'order';
		  	}
		  	return result;
		  }
		  $scope.titleIcon = function(s, order, order_dir) {
		  	//result '' or 'fa-caret-up' or 'fa-caret-down'
		  	var result = '';
		  	if ((s == order) & (order_dir == 'DESC')) {
		  		result = ' fa-caret-up';
		  	}
		  	if ((s == order) & (order_dir == 'ASC')) {
		  		result = ' fa-caret-down';
		  	}
		  	return result;
		  }
		  $scope.trClass = function() {
		  	//result 'tr0' or 'tr1'
		  	if ($scope.trClass == 'tr1') {
		  		$scope.trClass = 'tr0';
		  	} else {
		  		$scope.trClass = 'tr1';
		  	}
		  	return $scope.trClass;
		  }
		  $('#browserForm tbody tr').click(function() {
			  var member_id = this.id.substring(3,100);
			  $('#task').val('form');
			  $('#member_id').val(member_id);
			  $('#browserForm').submit();
		  });
	  } // browserForm
	  
	  $('#scope').show();
	  return 'members';
  }
  
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  $scope.onload = function() {
	  membersFun();
	  return '';
  };

  $scope.itemLoad = function($last) {
	  if ($last) {
		  membersFun();
	  }
	  return '';
  }
  
