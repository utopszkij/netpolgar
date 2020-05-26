  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function groupsFun() {
	  if ($('#formGroupForm').length) {
		  
		  // form init
		  $('#name').focus();
		  
		  global.invalidNumber = function(fieldName) {
			  $('#'+fieldName).addClass('is-invalid');
			  return $scope.txt('INVALID_NUMBER')+"<br />";
		  };
		  
		  $('#btnOK').click(function() {
			  var msgs = '';
			  $('#name').removeClass('is-invalid');
			  $('#description').removeClass('is-invalid');
			  $('#group_to_active').removeClass('is-invalid');
			  $('#group_to_close').removeClass('is-invalid');
			  $('#member_to_active').removeClass('is-invalid');
			  $('#member_to_close').removeClass('is-invalid');
			  if ($('#name').val() == '') {
				  msgs += $scope.txt('NAME_REQUED')+"<br />";
				  $('#name').addClass('is-invalid');
			  }
			  if ($('#description').val() == '') {
				  msgs += $scope.txt('DESCRIPTION_REQUED')+"<br />";
				  $('#description').addClass('is-invalid');
			  }
			  if (isNaN($('#group_to_active').val())) {
				  msgs += global.invalidNumber('group_to_active');
			  }
			  if (isNaN($('#group_to_close').val())) {
				  msgs += global.invalidNumber('group_to_close');
			  }
			  if (isNaN($('#member_to_active').val())) {
				  msgs += global.invalidNumber('member_to_active');
			  }
			  if (isNaN($('#member_to_exclude').val())) {
				  msgs += global.invalidNumber('member_to_axclude');
			  }
			  if (msgs == '') {
				  $('#formGroupForm').submit();
			  } else {
				  global.alert(msgs);
			  }
		  });
		  $('#btnCancel').click(function() {
			  location="{{MYDOMAIN}}/opt/groups/list";
		  });
		  $('#btnBack').click(function() {
			  location="{{MYDOMAIN}}/opt/groups/list";
		  });
		  $('#btnCandidate').click(function() {
			  $('#formGroupForm').attr('action',$scope.MYADMIN + '/opt/groups/candidate');
			  $('#formGroupForm').submit();
		  });
		  
		  $('#btnLogin').click(function() {
			  $('#formGroupForm').attr('action',$scope.MYADMIN + '/opt/groups/login');
			  $('#formGroupForm').submit();
		  });
		  $('#btnExit').click(function() {
			  $('#formGroupForm').attr('action',$scope.MYADMIN + '/opt/groups/exit');
			  $('#formGroupForm').submit();
		  });
		  $('#btnRemove').click(function() {
			  $('#formGroupForm').attr('action',$scope.MYADMIN + '/opt/groups/remove');
			  $('#formGroupForm').submit();
		  });
		  $('#btnAdd').click(function() {
			  $('#formGroupForm').attr('action',$scope.MYADMIN + '/opt/groups/add/parentid/'+$scope.item.id);
			  $('#formGroupForm').submit();
		  });
		  
		  $('#reg_mode').val($scope.item.reg_mode);
		  $('#state').val($scope.item.state);
		  if ($scope.userGroupAdmin) {
			  $('#btnBack').hide();
		  } else {	  
			  $('#formGroupForm input').attr('readonly','readonly');
			  $('#formGroupForm textarea').attr('readonly','readonly');
			  $('#formGroupForm select').attr('readonly','readonly');
			  $('#formGroupForm select').attr('disabled','disabled');
			  $('#btnOK').hide();
			  $('#btnCancel').hide();
			  $('#btnRemove').hide();
			  $('#btnAdd').hide();
		  }
		  if ($scope.item.id < 0) {
			  // virtuális root rekord
			  $('#formGroupForm input').attr('readonly','readonly');
			  $('#formGroupForm textarea').attr('readonly','readonly');
			  $('#formGroupForm select').attr('readonly','readonly');
			  $('#formGroupForm select').attr('disabled','disabled');
		  }
		  if (($scope.item.state == 'closed') | ($scope.item.state == 'proposal')) {
			  $('#btnCandidate').hide();
			  $('#btnLogin').hide();
			  $('#btnExit').hide();
		  } else if ($scope.userMember) {
			  $('#btnCandidate').hide();
			  $('#btnLogin').hide();
		  } else {
			  $('#btnExit').hide();
			  if (($scope.item.reg_mode == 'candidate') | ($scope.user.id == 0)) {
				  $('#btnLogin').hide();
			  }
			  if (($scope.item.reg_mode == 'self') | ($scope.user.id == 0)) {
				  $('#btnCandidate').hide();
			  }
			  if (($scope.item.reg_mode == 'admin') | ($scope.user.id == 0)) {
				  $('#btnLogin').hide();
				  $('#btnCandidate').hide();
			  }
		  }
	  } // form 
	  
	  if ($('#groupsList').length) {
		  if (!$scope.userGroupAdmin) {
			  $('#addSubGroup').hide();
		  }
	  } // groupList 
	  
	  $('#scope').show();
	  return 'groups';
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  groupsFun();
  
