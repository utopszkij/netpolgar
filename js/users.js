  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 

  function pageOnLoad() {
  }	

  function mainFun() {
	  if ($('#registForm').length) {
		  /**
		   * regmod selector változás eseménykezelő
		   */
		  $('#regmodSelect').change(function() {
			  if ($('#regmodSelect').val() == 'uklogin') {
				  $scope.reg_mode = 'uklogin';
				  $('#ukloginRegform').show();
				  $('#ifrmUklogin').attr('src',"https://uklogin.tk/opt/userregist/registform/client_id/12");
				  $('#webRegform').hide();
			  } else if ($('#regmodSelect').val() == 'web') {
				  $scope.reg_mode = 'web';
				  $scope.avatarUrl = '';
				  $('#imgAvatar').hide();
				  $('#reg_mode').val('web');
				  $('#ukloginRegform').hide();
				  $('#webRegform').show();
				  
				  // fokusz beállítása
				  if (($('#nick').val() == '') && ($('#nick').attr('disabled') != 'disabled')) {
					  $('#nick').focus();
				  } else {
					  $('#name').focus();
				  }
	
				  // inputban és textareaban ENTER click --> Ok click
				  $('input').keypress(function (e) {
					  if (e.which == 13) {
					    $('#btnOk').click();
					    return false;    //<---- Add this line
					  }
				  });
				  $('textarea').keypress(function (e) {
					  if (e.which == 13) {
					    $('#btnOk').click();
					    return false;    //<---- Add this line
					  }
				  });
				  
			  } else {
				  $scope.reg_mode = '';
				  $('#ukloginRegform').hide();
				  $('#webRegform').hide();
			  }
		  });
		  
	      $('#nick').attr('disabled',false);

		  /**
		   * avatar beíró mező változás eseménykezelő
		   */
		  $('#avatar').change(function() {
			  $scope.avatarUrl = $('#avatar').val();
			  $('#imgAvatar').attr('src',$scope.avatarUrl);
			  if ($scope.avatarUrl == '') {
				  $('#imgAvatar').hide();
			  } else {
				  $('#imgAvatar').show();
			  }
		  });
		  
		  /**
		   * avatar megjelenitő gomb click eseménykezelő
		   * @returns
		   */
		  $('#btnAvatarShow').click(function() {
				 $('#avatar').change();  
		  });
		  
		  /**
		   * OK gomb click eseménykezelő
		   */
		  $('#btnOk').click(function() {
			  var msg = '';
			  $('#nick').removeClass('is-invalid');
			  $('#psw').removeClass('is-invalid');
			  $('#name').removeClass('is-invalid');
			  $('#email').removeClass('is-invalid');
			  $('#psw2').removeClass('is-invalid');
			  
			  if ($('#nick').val() == '') {
				  $('#nick').addClass('is-invalid');
				  msg += $scope.txt('NICK_REQUED')+"<br />";
			  }
			  
			  if ($scope.id == 0) {
				  if ($('#psw').val() == '') {
					  $('#psw').addClass('is-invalid');
					  msg += $scope.txt('PSW_REQUED')+"<br />";
				  }
			  }
			  
			  if ($('#psw').val() != $('#psw2').val()) {
				  $('#psw').addClass('is-invalid');
				  $('#psw2').addClass('is-invalid');
				  msg += $scope.txt('PSWS_NOT_EQUALS')+"<br />";
			  }
			  if (($('#psw').val().length < 6) && ($('#psw').val() != '')) {
				  $('#psw').addClass('is-invalid');
				  msg += $scope.txt('PSWS_SORT')+"<br />";
			  }		  
			  
			  if ($('#name').val() == '') {
				  $('#name').addClass('is-invalid');
				  msg += $scope.txt('NAME_REQUED')+"<br />";
			  }
			  if ($('#email').val() == '') {
				  $('#email').addClass('is-invalid');
				  msg += $scope.txt('EMAIL_REQUED')+"<br />";
			  }
			  if (msg == '') {
				  $('#formRegform').submit(); 
			  } else {
				  global.alert(msg);
			  }
		  });
		  
		  /**
		   * cancel gomb click eseménykezelő
		   */
		  $('#btnCancel').click(function() {
			  if ($scope.cancelUrl != '') {
				  window.location = $scope.cancelUrl;
			  } else {
				  $('#ukloginRegform').hide();
				  $('#webRegform').hide();
				  $('#regmodSelect').val('');
			  }
		  });
		  
		  /**
		   * form init
		   */
		  if ($scope.avatarrUrl == '') {
			  $('#imgAvatar').hide();
		  } else {
			  $('#imgAvatar').show();
		  }
		  $('#regmodSelect').val($scope.reg_mode);
		  $('#regmodSelect').change();
		  if ($scope.id > 0) {
			  $('#nick').attr('disabled','disabled');
		  }
	  } // registformról van szó

	  
	  if ($('#usersList').length) {
	  }

	  $('#scope').show();
	  
	  if ($('#loginForm').length) {
		    $('#nick').attr('disabled',false);
			$('#nick').focus();	  
			$('#btnUklogin').click(function() {
				$('#ifrmUkloginLogin').attr('src', 'https://uklogin.tk/oauth2/loginform/client_id/12');
				$('#loginForm').hide();
				$('#divUkloginLogin').show();
			});
			$('#linkForgetPsw').click(function() {
				$('#formLogin').attr('action','<?php echo MYDOMAIN; ?>/opt/users/forgetpsw');
				$('#formLogin').submit();
				return false;
			});
			$('#linkGetActivateEmail').click(function() {
				$('#formLogin').attr('action','<?php echo MYDOMAIN; ?>/opt/users/getactivateemail');
				$('#formLogin').submit();
				return false;
			});
			
	  } // login formról van szó

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
  mainFun();
  
