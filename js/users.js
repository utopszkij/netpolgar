  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 

  function pageOnLoad() {
  }	

  function mainFun() {
	  
	  if ($('#usersList').length) {
	  }

	  $('#scope').show();

	  if ($('#divProfileForm').length) {
		  if ($scope.avatarUrl == '') {
			  $('#imgAvatar').hide();
		  } else {
			  $('#imgAvatar').show();
		  }
		  $('#nick').attr('disabled','disabled');
		  $('#name').focus();
		  $('#formRegform').attr('action',"<?php echo MYDOMAIN; ?>/opt/users/profilesave");
		  $('#btnOk').click(function() {
			  $('#formRegform').submit();
		  });
		  $('#btnCancel').click(function() {
			  location = $scope.backUrl;
		  });
		  $('#btnRemove').click(function() {
			  location='opt/users/removeaccount/userid/'+$('#id').val()+
			  '/p1/0'+
			  '?backUrl='+encodeURI($scope.backUrl);
		  });
		  
		  if ($scope.userData.id != $scope.loggedUser.id) {
			  // nem modosítható
			  $('input').attr('disabled','disabled');
			  $('textarea').attr('disabled','disabled');
			  $('select').attr('disabled','disabled');
			  $('#btnOk').hide();
			  $('#btnRemove').hide();
			  $('#btnCanvel span').html(txt('BACK'));
		  }
	  }
	  
	  global.reOrder = function(fieldName) {
		  $('#orderField').val(fieldName);
		  if ($scope.orderField == fieldName) {
			  if ($scope.orderDir == 'ASC') {
				  $('#orderDir').val('DESC');
			  } else {
				  $('#orderDir').val('ASC');
			  }	  
		  } else {
			  $('#orderDir').val('ASC');
		  }
		  $('#formUsersList').submit();
	  };

	  if ($('#usersList').length) {
		  
		  // aktuális rendezés jelzése a th-ban
		  var caret = 'fa-caret-down';
		  if ($scope.orderDir == 'DESC') {
			  caret = 'fa-caret-up';
		  }
		  $('th em').removeClass('fa-caret-down');
		  $('th em').removeClass('fa-caret-up');
		  if ($scope.orderField == 'id') {
			  $('#thId em').addClass(caret);
		  } else if ($scope.orderField == 'nick') {
			  $('#thNick em').addClass(caret);
		  } else if ($scope.orderField == 'name') {
			  $('#thName em').addClass(caret);
		  }

		  // thclick funkciók
		  $('th').attr('style','cursor:pointer');
		  $('#thId').click(function() {
			  global.reOrder('id');
		  });
		  $('#thNick').click(function() {
			  global.reOrder('nick');
		  });
		  $('#thName').click(function() {
			  global.reOrder('name');
		  });
	  }
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  $scope.onload = function() {
	  mainFun();
  }	  
  
