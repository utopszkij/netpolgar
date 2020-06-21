
//le kell tiltani, hogy ugyanaz az em click rutin rövid időn belül kétszer fusson
// erre szolgál ez a változó
global.disabledId = '';
  
// jquery page onload --- kötelező ez a funkció !
// here $scope is not valid. 
function jqueryOnLoad() {
}	


// a képernyő inicializálása
$scope.onload = function() {
	  if ($('#formGroupForm').length) {
		  
		  // focus cursor
		  $('#name').focus();
		  
		  // form inicializálása, select elenek beállítása
		  $('#reg_mode').val($scope.item.reg_mode);
		  $('#state').val($scope.item.state);
		  if ($scope.loggedUser == undefined) {
			  $scope.loggedUser = {"id":0};
		  }
		  
		  // a csoport adatain csak az "admin" -ok modosithatnak, ők törölhetnek.
		  if ($scope.userState != 'admin') {
			  $('#formGroupForm input').attr('readonly','readonly');
			  $('#formGroupForm textarea').attr('readonly','readonly');
			  $('#formGroupForm select').attr('readonly','readonly');
			  $('#formGroupForm select').attr('disabled','disabled');
			  $('#btnRemove').hide();
			  $('#btnOk').hide();
			  $('#btnCancel').hide();
			  $('#btnRemove').hide();
		  }
		  
		  if ($scope.item.id < 0) {
			  // virtuális root rekord
			  $('#formGroupForm input').attr('readonly','readonly');
			  $('#formGroupForm textarea').attr('readonly','readonly');
			  $('#formGroupForm select').attr('readonly','readonly');
			  $('#formGroupForm select').attr('disabled','disabled');
			  $('#btnRemove').hide();
		  }
		  
		  // lezárt  állapotban a csoporttal semmi nem csinálható
		  if ($scope.item.state == 'closed') {
			  $('#formGroupForm input').attr('readonly','readonly');
			  $('#formGroupForm textarea').attr('readonly','readonly');
			  $('#formGroupForm select').attr('readonly','readonly');
			  $('#formGroupForm select').attr('disabled','disabled');
			  $('#btnRemove').hide();
			  $('#btnOk').hide();
			  $('#btnCancel').hide();
			  $('#btnRemove').hide();
			  $('#btnCandidate').hide();
			  $('#btnLogin').hide();
			  $('#btnExit').hide();
		  }

		  // látogatók semmit nem csinálhatnak
		  if ($scope.loggedUser.id <= 0) {
			  $('#btnOk').hide();
			  $('#btnCancel').hide();
			  $('#btnRemove').hide();
			  $('#btnCandidate').hide();
			  $('#btnLogin').hide();
			  $('#btnExit').hide();
		  }
		  
		  // csak tagok léphetnek ki
		  if ($scope.userState != 'active') {
			  $('#btnExit').hide();
		  }
		  
		  // új felvitelnés a sátusz csak "proposal" lehet és nem modosítható
		  if ($scope.item.id == 0) {
			  $('#state').val('proposal');
			  $('#state').attr('disabled','disabled');
		  }
		  
		  // like gombok és értékek inicializálása, like btnclick -ek success eljátrása is hivja 
		  $scope.likeAdjust = function() {
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
		  
		  // process ENTER key put
		  $('#formGroupForm').keyup(function (event) {
			  if ( event.which == 13 ) {
				   $('#btnOK').click();
			  }			  
		  });
		  
		  // form validátor segéd funkciók
		  global.invalidNumber = function(fieldName) {
			  $('#'+fieldName).addClass('is-invalid');
			  return $scope.txt('INVALID_NUMBER')+"<br />";
		  };
		  
		  // click esemény kezelők
		  $('#likeUpBtn').click( function() {
			  console.log($scope.like.upChecked);
			  if ($('#likeUpBtn').attr('disabled') != 'disabled') {
				  $('#likeUpBtn').attr('disabled','disabled');
				  var url = $scope.MYADMIN+'/opt/likes/setlike/type/group/id/'+$scope.id;
				  $.get(url, function(result) {
					  window.setTimeout($scope.likeAdjust,500);
				  });
			  }  
		  });
		  $('#likeDownBtn').click( function() {
			  if ($('#likeDownBtn').attr('disabled') != 'disabled') {
				  $('#likeDownBtn').attr('disabled','disabled');
				  var url = $scope.MYADMIN+'/opt/likes/setdislike/type/group/id/'+$scope.id;
				  $.get(url, function(result) {
					  window.setTimeout($scope.likeAdjust,500);
				  });
			  }  
		  });
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
	  } // form 
	  
	  if ($('#groupsList').length) {
		  if (!$scope.userGroupAdmin) {
			  $('#addSubGroup').hide();
		  }
		  $scope.treeInit();
	  } // groupList 

	  if ($('#groupsListByUser').length) {
		  if (!$scope.userGroupAdmin) {
			  $('#addSubGroup').hide();
		  }
		  $('#searchBtn').click(function() {
			  $('#offset').val(0);
			  $('#groupsListByUser').submit();
		  });
		  $('#delSearchBtn').click(function() {
			  $('#filter_str').val('');
			  $('#offset').val(0);
			  $('#groupsListByUser').submit();
		  });
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
		  };
		  $scope.trClass = function() {
			  	//result 'tr0' or 'tr1'
			  	if ($scope.trClass == 'tr1') {
			  		$scope.trClass = 'tr0';
			  	} else {
			  		$scope.trClass = 'tr1';
			  	}
			  	return $scope.trClass;
		  };
		  $('#groupsListByUser tbody tr').click(function() {
				  var group_id = this.id.substring(3,100);
				  alert('item click '+group_id);
		  });

	  } // groupsListByUser
	  $('#scope').show();
	  return 'groups';
 }; // groupsFun

 function titleClick(name,order,order_dir) {
	 
 }
 
 
/**
* a subgroup nem biztos, hogy be van olvasva (elöször csak egy üres ul kerül kialakitásra)
* tehát lehet, hogy be kell olvasni az adatbázisból AJAX hívással
* @param JqueryUlObject subGroup
* @param int parentId 
* @param function() success function
*/
 $scope.loadSubGroup = function(subGroup, parentId, successFun) {
	 // be van már olvasva?
     if (subGroup[0].childElementCount <= 0) {
	        global.working(true);
	        // ajax server result: {parentId:###, items:[{id,name,childs:bool}..... ]}
	        // ajaxhivás(parentId, function(result) {
	        var url = '<?php echo MYDOMAIN?>/opt/groups/loadsubgroup';
	        var data = {"parentId": parentId};
	        global.post(url, data,  function(res) {
		        		//  res.items elemekkel az ul feltöltése
						var parentId = res.parentId;
						var ul = $('#i_'+parentId+' ul:first');
						for (var i=0; i < res.items.length; i++) {
							if (res.items[i].childs) {
								var newLi = $('<li id="i_'+res.items[i].id+'">'+
										'<em class="fa fa-plus-square" style="cursor:pointer"></em>'+
										'<var>'+
										'<img class="groupIcon" src="'+res.items[i].avatar+'" />'+
										res.items[i].name+
										'</var></li>');
								ul.append(newLi);
								var newUl = '<ul style="display:none"></ul>';
								$('#i_'+res.items[i].id).append(newUl);
							} else {
								var newLi = $('<li id="i_'+res.items[i].id+'">'+
										'<em></em>'+
										'<var>'+
										'<img class="groupIcon" src="'+res.items[i].avatar+'" />'+
										res.items[i].name+
										'</var></li>');
								ul.append(newLi);
							}
						}
	    		        global.working(false);
	    		        $scope.treeInit('i_'+parentId);
	    		        successFun();
	        });
      } else {
	        successFun();
	  }
};

$scope.treeInit = function(parentId) {
		if (parentId == undefined) {
			parentId = 'groupsTree';
		}
		// em click rutin
		$('#'+parentId+' em').click(function() {
			var itemId = this.parentNode.id;
			if ((itemId == '') | (itemId == undefined) | (itemId == global.disabledId)) {
				return;
			}
			global.disabledId = itemId; 
			var item = $('#'+itemId);
			var subgroup = item.find('ul:first');
			var em = item.find('em:first');
			if (subgroup.is(':hidden')) {
				$scope.loadSubGroup(subgroup, itemId.substr(2,100), function() {
				     subgroup.show();
				     em.removeClass('fa-plus-square');		
				     em.addClass('fa-minus-square');		
				});
				subgroup.show();
				em.removeClass('fa-plus-square');		
				em.addClass('fa-minus-square');		
			} else {				
				subgroup.hide();
				em.removeClass('fa-minus-square');		
				em.addClass('fa-plus-square');		
			}
			window.setTimeout('global.disabledId="";',500);			
		});

		// name click rutin
		$('#'+parentId+' var').click(function() {
			var itemId = this.parentNode.id.substr(2,100);
			window.location='<?php echo MYDOMAIN; ?>/opt/groups/form/id/'+itemId+
			'/'+$scope.csrToken+'/1';
		});
		return '';
}; // treeInit
  
	
$(function() {
    // jquery pageOnLoad
  	jqueryOnLoad();
});	
  
// az items ciklus minden sorának kiirása után hívódik.
// a $last = true jelti, hogy ez volt az utolsó elem
$scope.itemLoad = function($last) {
	if ($last) {
		$scope.onload();
	}
	return '';
}

