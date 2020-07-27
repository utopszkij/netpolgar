  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !
  function pageOnLoad() {
  }	

  function membersFun() {
	  if ($('#browserForm').length) {
		  $scope.filterStateSelected = function(s,filterstate) {
		  	//result: '' or ' "selected"="selected"
			var result = '';
			if (s == filterstate) {
				result = ' selected="selected"';
			}
			return result;  
		  };
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
		  };
		  $('#browserForm tbody tr').click(function() {
			  var member_id = this.id.substring(3,100);
			  $('#task').val('form');
			  $('#member_id').val(member_id);
			  $('#browserForm').submit();
		  });
		  $('#browserForm thead th').click(function() {
			  var name = this.id.substring(3,100);
			  if (name == $scope.order) {
				  if ($scope.order_dir == 'ASC') {
					  $('#order_dir').val('DESC');
				  } else {
					  $('#order_dir').val('ASC');
				  }
			  } else {
				  $('#order').val(name);
				  $('#order_dir').val('ASC');
			  }
			  $('#offset').val(0);
			  $('#browserForm').submit();
		  });
		  $('#searchBtn').click(function() {
				 $('#offset').val(0); 
				 $('#browserForm').submit(); 
		  });
		  $('#delSearchBtn').click(function() {
				 $('#searchstr').val(''); 
				 $('#offset').val(0); 
				 $('#browserForm').submit(); 
		  });
		  $('#searchstr').keyup(function(event) {
			  if ( event.which == 13 ) {
				   $('#searchBtn').click();
			  }			  
		  });
	  } // browserForm
	  
	  if ($('#memberForm').length) {
		  $('#likeUpBtn').click( function() {
			  if ($('#likeUpBtn').attr('disabled') != 'disabled') {
				  $('#likeUpBtn').attr('disabled','disabled');
				  var url = $scope.MYDOMAIN+'/opt/likes/setlike/type/members/id/'+$scope.id;
				  $.get(url, function(result) {
					  // '{up, down, upChecked, downChecked, state}'
					  $scope.likeCount = JSON.parse(result);
					  $scope.userState = $scope.likeCount.state;
					  $('#userState').html($scope.state);
					  window.setTimeout($scope.likeAdjust,500);
				  });
			  }  
		  });
		  $('#likeDownBtn').click( function() {
			  if ($('#likeDownBtn').attr('disabled') != 'disabled') {
				  $('#likeDownBtn').attr('disabled','disabled');
				  var url = $scope.MYDOMAIN+'/opt/likes/setdislike/type/members/id/'+$scope.id;
				  $.get(url, function(result) {
					  // '{up, down, upChecked, downChecked, state}'
					  $scope.likeCount = JSON.parse(result);
					  $scope.userState = $scope.likeCount.state;
					  $('#userState').html($scope.state);
					  window.setTimeout($scope.likeAdjust,500);
				  });
			  }  
		  });

		  // like gombok és értékek inicializálása, like btnclick -ek success eljátrása is hivja 
		  $scope.likeAdjust = function() {
			  $scope.userMember = (($scope.userState == 'active') || ($scope.userState == 'admin'));
			  if ($scope.userMember && 
				  (($scope.item.state == 'active') || ($scope.item.state == 'proposal'))) {
				  $('#likeUpBtn').attr('disabled',false);
				  $('#likeDownBtn').attr('disabled',false);
			  } else {
				  $('#likeUpBtn').attr('disabled','disabled');
				  $('#likeDownBtn').attr('disabled','disabled');
			  }
			  $('#likeUpBtn var').html($scope.likeCount.up);
			  $('#likeDownBtn var').html($scope.likeCount.down);
			  if ($scope.likeCount.upChecked) {
				  $('#likeUpBtn em.fa-check').show();
			  } else {
				  $('#likeUpBtn em.fa-check').hide();
			  }
			  if ($scope.likeCount.downChecked) {
				  $('#likeDownBtn em.fa-check').show();
			  } else {
				  $('#likeDownBtn em.fa-check').hide();
			  }
		  };
		  $scope.likeAdjust();
		  

	  } // memberForm	  
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

  $scope.itemLoad = function(last) {
	  if (last) {
		  membersFun();
	  }
	  return '';
  }
  
