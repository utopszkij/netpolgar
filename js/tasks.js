  
  // params for controller	  
  // param1 requested
  // sid string requested

  // jquery page onload --- must this function !
  function pageOnLoad() {
  }	

  function tasksFun() {
	  
	    // kanban táblához globális funkciok
		function cardDraggable() {
			$('.cardDrag').draggable({
				start: function(event, ui) {
					ui.helper.css('zIndex',100);
				}
			});
		}
		function renderState(state,$scope) {
			for (var i = 0; i < $scope.items.length; i++) {
				if ($scope.items[i].state == state) {
					var task = $scope.items[i];
					var taskClass = 'cardFix'
					if (($scope.loggedState == 'admin') | (task.nick == $scope.loggedUser.nick)) {
						taskClass = 'cardDrag'
					}
					var cardHtml = '<div class="card '+taskClass+' '+task.tasktype+'"'+
					    ' id="'+task.id+'" sequence="'+task.sequence+'">';
					if (task.nick != '') {
						cardHtml += '<p class="nick"><strong>#'+task.id+'</strong> <img src="'+task.avatar+'" />'+task.nick+'</p>';
					} else {
						cardHtml += '<p class="nick"><strong>#'+task.id+'</strong></p>';
					}	
					cardHtml += '<p class="desc">'+task.description+'</p>';
					cardHtml += '</div>';
					$('#'+state).append(cardHtml);
				}
			}
		}
		function renderCards($scope) {
			if ($scope.rendered == undefined) {
				renderState('wait_req',$scope);
				renderState('wait_run',$scope);
				renderState('runing',$scope);
				renderState('wait_control',$scope);
				renderState('closed',$scope);
				$scope.rendered = true;
				cardDraggable();
			}	
		}
		function updateState(id, state, sequence) {
			$.post( './index.php/opt/tasks/newstate/id/'+id+
					'/state/'+state+'/sequence/'+sequence, function( data ) {
				  data = data.replace(/\n/,'');
				  data = data.replace(/\r/,'');
				  if (data != 'OK') {
					  global.alert([data]);
					  if (data.substring(0,1) == '!') {
						// nem futtatható  
					  	$('#wait_req').append($('#'+id));
					  }
				  }
			});
		}
		
		if ($('#browserForm').length > 0) {
		  global.pause = false; // gyors egymásutáni click tiltása
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
			location = $scope.MYDOMAIN+'/opt/tasks/add/project_id/'+$scope.project_id;
		  });
		  
		  
		  // kanban funciók
		  renderCards($scope);
		  if ($scope.loggedState != 'none') {
				$('.cardDrag').draggable({
					start: function(event, ui) {
						ui.helper.css('zIndex',100);
					}
				});
		  }
		  $('.card').click(function() {
				location = './index.php/opt/tasks/form/id/'+this.id;
		  });
		  $('.kanbanCol h4 em').click(function() {
				var colName = this.parentNode.parentNode.id;
				console.log('em click ',colName, this.className);
				if (global.pause == false) {
					global.pause = true;
					if (this.className == 'fa fa-caret-square-up') {
						$('#'+colName).css('overflow','hidden');
						$('#'+colName).css('min-height',60);
						$('#'+colName).css('height',60);
						this.className = 'fa fa-caret-square-down';
					} else if (this.className == 'fa fa-caret-square-down') {
						$('#'+colName).css('min-height',80);
						$('#'+colName).css('height','auto');
						this.className = 'fa fa-caret-square-up';
					}
					window.setTimeout('global.pause = false',500);
				}
			});
			$('.kanbanCol').droppable({
				drop: function(event, ui) {
					// helper a huzott elem
					// this erre a DOM elemre lett huzva
					ui.helper.css('left',0);
					ui.helper.css('top',0);
					ui.helper.css('zIndex',1);
					$('#'+this.id).append(ui.helper);
					// window.setTimeout('cardDraggable()',500);
					updateState(ui.helper.attr('id'), this.id, 'max');
				}
			});
			$('.card').droppable({
					drop: function(event, ui) {
						// helper a huzott elem
						// this erre a DOM elemre lett huzva
						ui.helper.css('left',0);
						ui.helper.css('top',0);
						ui.helper.css('zIndex',1);
						var thisCard = $('#'+this.id);
						ui.helper.insertAfter(thisCard);
						// window.setTimeout('cardDraggable()',500);
						updateState(ui.helper.attr('id'), 
								thisCard.parent().attr('id'),
								thisCard.attr('sequence'));
					}
			});
			$('.kanbanCol h4').droppable({
				drop: function(event, ui) {
					// helper a hozott elem
					// this erre a DOM elemre lett huzva
					ui.helper.css('left',0);
					ui.helper.css('top',0);
					ui.helper.css('zIndex',1);
					ui.helper.insertAfter($('#'+this.parentNode.id+' h4'));
					// window.setTimeout('cardDraggable()',500);
					updateState(ui.helper.attr('id'), this.parentNode.id, 'first');
				}
			});
			$('body').droppable({
				// hibás helyre huzta a card -ot
				drop: function(event, ui) {
					if (ui.helper.css('zIndex') > 1) {
						location='./index.php/opt/tasks/list';
					}	
				}
			});
		    
	  } // browserForm
	  
	  if ($('#taskForm').length > 0) {

		  // form inicializálása, select elenek beállítása
		  $('#state').val($scope.item.state);
		  $('#tasktype').val($scope.item.tasktype);
		  
		  if ($scope.loggedUser == undefined) {
			  $scope.loggedUser = {"id":0};
		  }
		  // process ENTER key put
		  $('#taskForm').keyup(function (event) {
			  if ( event.which == 13 ) {
				   $('#btnOK').click();
			  }			  
		  });

		  // nem projekt tag nem modosithat
		  if (($scope.loggedState == 'none') && ($scope.item.id > 0)) {
			$('#description').attr('disabled','disabled');
			$('#state').attr('disabled','disabled');
			$('#deadline').attr('disabled','disabled');
			$('#tasktype').attr('disabled','disabled');
			$('#reqclosed').attr('disabled','disabled');
			$('#reqnotrun').attr('disabled','disabled');
			$('#nick').attr('nick','disabled');
			$('#sequence').attr('sequence','disabled');
			$('#btnOk').hide();  
		  } else {
			// focus cursor
			$('#description').focus();
		  }
		  
		  
		  // felvitelnél a state, sequence nem modosítható
		  if ($scope.item.id == 0) {
				$('#state').attr('disabled','disabled');
				$('#sequence').attr('disabled','disabled');
		  }
		  
		  $scope.isValidDate = function(s) {
			  var bits = s.split('-');
			  var d = new Date(bits[0] + '-' + bits[1] + '-' + bits[2]);
			  return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[2]));
		  };	
		  
		  $('#btnOk').click(function() {
			  // ellenörzések
			  var msgs = '';
			  if ($('#description').val() == '') {
				  msgs += $scope.txt('DESCRIPTION_REQUESTED')+"<br />";
				  $('#description').addClass('is-invalid');
			  }
			  var s = $('#deadline').val();
			  if (!$scope.isValidDate(s)) {
				  msgs += $scope.txt('DATE_INVALID')+"<br />";
				  $('#deadline').addClass('is-invalid');
			  }
			  if (msgs == '') {
				  $('#taskForm3').submit();
			  } else {
				  global.alert(msgs);
			  }	  
		  });
		  $('#delBtn').click(function() {
			 global.confirm($scope.txt('SURE_DELETE'), function() {
				location = $scope.MYDOMAIN+'/opt/tasks/delete/id/'+$('#id').val();
			 });
			 return false;
		  });
	  } // taskForm	  
	  $('#scope').show();
	  return '';
  }

  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  $scope.onload = function() {
	  tasksFun();
	  return '';
  };

  $scope.itemLoad = function($last) {
	  if ($last) {
		  tasksFun();
	  }
	  return '';
  }
    
  
  

  
