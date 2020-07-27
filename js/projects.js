  
  // params for controller	  
  // param1 requested
  // sid string requested

  // jquery page onload --- must this function !
  function pageOnLoad() {
  }	

  function projectsFun() {
	  if ($('#browserForm').length > 0) {
		  
		  $('#filterState').val($scope.filterState);
		  
		  $scope.filterStateSelected = function(s,filterstate) {
		  	//result: '' or ' "selected"="selected"
			var result = '';
			if (s == filterstate) {
				result = ' selected="selected"';
			}
			return result;  
		  };
		  $scope.thClass = function(s, order, order_dir)  {
		  	//result 'unorder' or 'order'
		  	var result = 'unorder';
		  	if (s == order) {
		  		result = 'order';
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
		  }
		  $('#browserForm tbody tr').click(function() {
			  var id = this.id.substring(3,100);
			  $('#task').val('form');
			  $('#id').val(id);
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
		  $('#btnAdd').click(function() {
			location = $scope.MYDOMAIN+'/opt/projects/add';
		  });
		  if ($scope.loggedUser.id <= 0) {
			  $('#btnAdd').hide();
		  }
	  } // browserForm
	  
	  if ($('#projectForm').length > 0) {

		  // form inicializálása, select elenek beállítása
		  $('#state').val($scope.item.state);
		  if ($scope.loggedUser == undefined) {
			  $scope.loggedUser = {"id":0};
		  }
		  
		  // process ENTER key put
		  $('#projectForm').keyup(function (event) {
			  if ( event.which == 13 ) {
				   $('#btnOK').click();
			  }			  
		  });

		  // csak project admin modosithat, 
		  // csak bejelentkezett user vihet fel
		  // lezárt projekt nem modosítható
		  if ((($scope.loggedState != 'admin') && ($scope.item.id > 0)) || 
			  ($scope.loggedUser.id <= 0) ||
			  ($scope.state == 'closed')
			 ) {
			$('#name').attr('disabled','disabled');
			$('#description').attr('disabled','disabled');
			$('#state').attr('disabled','disabled');
			$('#avatar').attr('disabled','disabled');
			$('#deadline').attr('disabled','disabled');
			$('#project_to_active').attr('disabled','disabled');
			$('#project_to_close').attr('disabled','disabled');
			$('#member_to_active').attr('disabled','disabled');
			$('#member_to_exclude').attr('disabled','disabled');
			$('#btnOk').hide();  
		  } else {
			// focus cursor
			$('#name').focus();
		  }
		  
		  $scope.isValidDate = function(s) {
			  var bits = s.split('-');
			  var d = new Date(bits[0] + '-' + bits[1] + '-' + bits[2]);
			  return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[2]));
		  };	
		  
		  $('#btnOk').click(function() {
			  // ellenörzések
			  var msgs = '';
			  if ($('#name').val() == '') {
				  msgs += $scope.txt('NAME_REQUESTED')+"<br />";
				  $('#name').addClass('is-invalid');
			  }
			  var s = $('#deadline').val();
			  if (!$scope.isValidDate(s)) {
				  msgs += $scope.txt('DATE_INVALID')+"<br />";
				  $('#deadline').addClass('is-invalid');
			  }
			  if (isNaN($('#project_to_active').val())) {
				  msgs += $scope.txt('INVALID_NUMBER')+"<br />";
				  $('#project_to_acitive').addClass('is-invalid');
			  }
			  if (isNaN($('#project_to_close').val())) {
				  msgs += $scope.txt('INVALID_NUMBER')+"<br />";
				  $('#project_to_close').addClass('is-invalid');
			  }
			  if (isNaN($('#member_to_active').val())) {
				  msgs += $scope.txt('INVALID_NUMBER')+"<br />";
				  $('#member_to_active').addClass('is-invalid');
			  }
			  if (isNaN($('#member_to_exclude').val())) {
				  msgs += $scope.txt('INVALID_NUMBER')+"<br />";
				  $('#member_to_exclude').addClass('is-invalid');
			  }
			  if (msgs == '') {
				  console.log('OK');
				  console.log($('#projectForm3'));
				  $('#projectForm3').submit();
			  } else {
				  global.alert(msgs);
			  }	  
		  });
		  $('#likeUpBtn').click( function() {
			  if ($('#likeUpBtn').attr('disabled') != 'disabled') {
				  $('#likeUpBtn').attr('disabled','disabled');
				  var url = $scope.MYDOMAIN+'/opt/likes/setlike/type/projects/id/'+$scope.item.id;
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
				  var url = $scope.MYDOMAIN+'/opt/likes/setdislike/type/projects/id/'+$scope.item.id;
				  $.get(url, function(result) {
					  // '{up, down, upChecked, downChecked, state}'
					  $scope.likeCount = JSON.parse(result);
					  $scope.userState = $scope.likeCount.state;
					  $('#userState').html($scope.state);
					  window.setTimeout($scope.likeAdjust,500);
				  });
			  }  
		  });

		  // like gombok és értékek inicializálása, like btnclick -ek success eljárása is hivja 
		  $scope.likeAdjust = function() {
			  $scope.userMember = (($scope.loggedState == 'active') || ($scope.loggedState == 'admin'));
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
		  
	  } // projectForm	  
	  $('#scope').show();
	  return 'projects';
  }

  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  $scope.onload = function() {
	  projectsFun();
	  return '';
  };

  $scope.itemLoad = function($last) {
	  if ($last) {
		  projectsFun();
	  }
	  return '';
  }
    
  
  

  
