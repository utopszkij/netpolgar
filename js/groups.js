//le kell tiltani, hogy ugyanaz az em click rutin rövid időn belül kétszer fusson
// erre szolgál ez a változó
global.disabledId = '';
  
// jquery page onload --- kötelező ez a funkció !
// here $scope is not valid. 
function pageOnLoad() {
}	


// a képernyő inicializálása
function groupsFun() {
	  if ($('#formGroupForm').length) {
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
		  if ($scope.user == undefined) {
			  $scope.user = {"id":0};
		  }
		  if ($scope.userMember == undefined) {
			  $scope.userMember = false;
		  }
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
		  // userState szerint a like/dislike események kezelése 
		  // (#divLike em.fa-thumbs-up) és (#divLike em.fa-thumbs-up) style="cursor:pointer" és
		  // eseménykezelő hozzá likeUpClick('group',group.id, loggedUser.id, userState)
		  //                     likeDownClick('group',group.id, loggedUser.id, userState)
	  } // form 
	  
	  if ($('#groupsList').length) {
		  if (!$scope.userGroupAdmin) {
			  $('#addSubGroup').hide();
		  }
		  $scope.treeInit();
	  } // groupList 
	  
	  $('#scope').show();
	  return 'groups';
 } // groupsFun
  
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
			window.location='<?php echo MYDOMAIN; ?>/opt/groups/groupform/groupid/'+itemId+
			'/<?php echo $p->csrToken; ?>/1';
		});
		
}; // treeInit
  
	
$(function() {
    // jquery pageOnLoad
  	pageOnLoad();
});	
  
// az items ciklus minden sorának kiirása után hívódik.
// a $last = true jelti, hogy ez volt az utolsó elem
$scope.itemLoad = function($last) {
	if ($last) {
		groupsFun();
	}
	return '';
}
