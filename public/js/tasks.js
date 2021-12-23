// kell hozzá:
// <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
// <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  
  
  // globals	
  var saveTimer = 0;	       // windows timer handler 
  var refreshTimer = 0;	       // windows timer handler
  var oldAssign = '';	       // use in checTaskForm
  var dbRefreshEnable = true;  // false at saveToDatabase use in refreshFromdatabase
  var dbSaveEnable = true;     // false at RefresFromDatabase use in saveToDatabase
  var fileTime = 0;            // use in refreshFromDatabase
  var atDragging = false;      // mark at dragging, use refreshFromDatabase
  var refreshTime = 0;         // refresh time milisec
  
  // params for controller	  
  // projectId string  requed	
  // loggedUser string requed
  // users array [avatarurl,nickname],...] project' members  OPTIONAL
  // admins array [avatarurl]    OPTIONAL
  // sid string requed
  // REFRESHMIN  sec
  // REFRESHMAX  sec
  // SESSIONCOUNT session counts
 
  
  /**
   * convert task domm element into json string
   * @param domElement task
   * @param string projectid 
   * @param staring state
   * @returns string
   */
  function taskToJson(task) {
	  if (task.find('id')[0] == undefined) {
		  return '{}';
	  }
	   return '{'+
	   '"id":"'+task.find('id')[0].innerHTML+'", '+
	   '"title":'+JSON.stringify(task.find('title')[0].innerHTML)+', '+
	   '"desc":'+JSON.stringify(task.find('desc')[0].innerHTML)+', '+
	   '"type":"'+task.find('type')[0].className+'", '+
	   '"req":'+JSON.stringify(task.find('req')[0].innerHTML)+', '+
	   '"assign":"'+task.find('img').attr('avatar')+'"'+
	   '}';
  }
  
 /**
  * convert #database state into json string
  * @param state
  * @return string
  */ 
 function stateToJson(state) {
   var tasks = $(state).find('task');
   var i;
   var result = '"'+state+'": [';
   var task = null;   
   for (i=0; i<tasks.length; i++) {
	   task = $('#'+tasks[i].id);
	   if (i > 0) {
		   result += ', ';
	   }
	   result += taskToJson(task);
   }
   return result+'], '	 
 } 
 
 /**
  * convert #database members into json string
  * @return string
  */
 function membersToJson() {
	var result = '['; 
	var i;
	var members = $('member');
	for (i=0; i<members.length; i++) {
		if (i > 0) {
			result += ', ';
		}
		result += '{"avatar":"'+members[i].getAttribute('avatar')+'", ';
		result += '"admin":"'+members[i].getAttribute('admin')+'", ';
		result += '"nick":'+JSON.stringify(members[i].innerHTML)+'}';
	}
	result += ']';
	return result; 
 }
  
  
 /**
 * save complette project into database
 * server input: act='save', projectId, project 
 * server result: fileTime
 *  use global fileTime
 */
 function saveToDatabase(projectId) {
 	if (dbSaveEnable) {
 		dbRefreshEnable = false;

 	   // #database convert into  json string	
	   var s = '{';
	   s += stateToJson('waiting');
	   s += stateToJson('canstart');
	   s += stateToJson('atwork');
	   s += stateToJson('canverify');
	   s += stateToJson('atverify');
	   s += stateToJson('closed');
	   s += membersToJson();
	   s += '}';
	   if (projectId == 'demo') {
	 			dbRefreshEnable = true;
	   } else {
		 	global.post('./app.php', {"option":"tasks", "task":"save", "projectid":projectId, "project": s, "sid": sid}, function(res) {
		 		fileTime = res.fileTime;
	 			dbRefreshEnable = true;
		 	})
	   }
 	} else {
 		 clearTimeout(saveTimer);
 	 	 saveTimer = window.setTimeout("saveToDatabase(projectId)", 5000);
 	}
 }
 
 /**
  * save all members into database
  * server input: act='save', projectId, project 
  * server result: fileTime
  * use global fileTime
  */
  function saveAllMembers(projectId) {
  	if (dbSaveEnable) {
  		dbRefreshEnable = false;
 	   var s = membersToJson();
 	   
 	   if (projectId == 'demo') {
 	 			dbRefreshEnable = true;
 	   } else {
 		 	global.post('./app.php', {"option":"members", "task":"saveallmembers", "projectid":projectId, "members": s, "sid": sid}, function(res) {
 	 			dbRefreshEnable = true;
 		 	})
 	   }
  	} else {
  		 clearTimeout(saveTimer);
  	 	 saveTimer = window.setTimeout("saveAllMembers(projectId)", 5000);
  	}
  }
 
 /**
 * a div id="divId" oszolba appandeli
 * a stateObj -et
 * @param stateObk [{id, title, desc, type, assign, req},..]
 * @param string #divId
 * @return a div -be:
			<task id="...">
			  <id>...</id>
			  <title>...</title>
			  <desc>...</desc>
			  <type class="..task.type..">&nbsp;</type>
			  <assign><img src="..task.assign.." avatar="..task.assign.." title="..nick.."/></assign>
			  <req>...</req>
			</task>  
 */
 function appendState(stateObj, stateName) {
	 var i,j;
	 var task = null;
	 var nick = '';
	 var s = '';
	 if (stateObj == undefined) {
		 return;
	 }
	 if ((stateObj.length != undefined) && (stateObj.length > 0)) {
		 for (i=0; i < stateObj.length; i++) {
			task = stateObj[i];
			nick = '?';
			for (j=0; j < users.length; j++) {
				if (users[j][0] == task.assign) {
					nick = users[j][1];
				}
			}
			s = '<task id="'+task.id+'">';
			s += '<id>'+task.id+'</id>';
			s += '<title>'+task.title+'</title>';
			s += '<desc>'+task.desc+'</desc>';
			s += '<type class="'+task.type+'">&nbsp;</type>';
			s += '<assign><img src="'+task.assign+'" avatar="'+task.assign+'" title="'+nick+'"/></assign>';
			s += '<req>'+task.req+'</req>';
			$(stateName).append(s);
		 }
	 }
 }
 
 /**
 * refresh task from database
 * server input: act='refresh', projectId
 * server result: fileTime, project (json string)
 *  use global fileTime
 */
 function refreshFromDatabase(projectId, fun) {
 	if ((dbRefreshEnable) && (!atDragging)) {
 		dbSaveEnable = false;
 		global.post('./app.php', {"option":"tasks", "task":"refresh", 
 			"projectid":projectId, "fileTime": fileTime}, function(res) {
	 		var i, member;
			var s = '';
 			fileTime = res.fileTime;
	 		if (res.project != undefined) {
	 			// json project --> #database html dom
	 			$('task').remove();
	 			appendState(res.project.waiting, 'waiting');
	 			appendState(res.project.canstart, 'canstart');
	 			appendState(res.project.atwork, 'atwork');
	 			appendState(res.project.canverify, 'canverify');
	 			appendState(res.project.atverify, 'atverify');
	 			appendState(res.project.closed, 'closed');
		        colTranslate(); 
		        colResize();
	 			setTaskEventHandlers();
	 			if (res.project.members != undefined) {
	 				$('member').remove();
	 				for (i=0; i< res.project.members.length; i++) {
	 					member = res.project.members[i];
	 					s = '<member';
	 					s += ' avatar="'+member.avatar+'"';
	 					s += ' admin="'+member.admin+'">';
	 					s += member.nick;
	 					s += '</member>';
	 					$('members').append(s);
	 				}
	 			}
	 		}
 			dbSaveEnable = true;
 			if (fun != undefined) {
 				fun();
 			}	
 		    clearTimeout(refreshTimer);
	  	    refreshTimer = window.setTimeout("refreshFromDatabase(projectId)", refreshTime);
	 	})
 	} else {
 		 dbSaveEnable = true;
 		 clearTimeout(refreshTimer);
 	 	 refreshTimer = window.setTimeout("refreshFromDatabase(projectId)", refreshTime);
 	}
 }

 function getIdMax() {
		var result = 0;
		var tasks = $('task');
		var i;
		var j = 0;
		for (i=0; i<tasks.length; i++) {
			j = Number(tasks[i].id);
			if ((j > result) && (tasks[i].id != 'taskInit')) {
				result = j;			
			}		
		}
		return result; 
 }
 
 function userMember() {
 	var result = false;
 	var i;
 	var members = $('members').find('member');
 	for (i=0; i < members.length; i++) {
		if (members[i].getAttribute('avatar') == loggedUser) {
			result = true;		
		} 	
 	}
	return result; 
 }

 function loggedAdmin() {
 	var result = false;
 	var i;
 	var members = $('members').find('member');
 	for (i=0; i < members.length; i++) {
		if ((members[i].getAttribute('avatar') == loggedUser) &&
		    (members[i].getAttribute('admin') == "1")) {
			result = true;		
		} 	
 	}
	return result; 
 }

 function getClosedTasks() {
	var result = [];
	var tasks = $('closed').find('task');
	var i;
	for (i=0; i<tasks.length; i++) {
		result.push(tasks[i].id);	
	}
	return result; 
 }	

 /**
 * taskForm validation
 * @param JQueryObject
 * @return bool, alert errorMsg
 */	
 function checkForm(taskForm) {
   // check state from taskForm
   var result = true;
 	var newState = taskForm.find('#state').val();
 	var req = taskForm.find('#req').val();
 	result = checkState3(newState, req);
 	if (result) {
		var assign = taskForm.find('#assign').val();
		if ((oldAssign != assign) && /* hozzányult */ 
			(loggedAdmin() == false) && /* nem admin */
		    ((assign != loggedUser) || (oldAssign != 'https://www.gravatar.com/avatar/'))  /* nem önmgához rendelte vagy nem volt üres */
		) {
			global.alert("<?php echo ACCESSDENIED; ?>");
			result = false;		
		}			 
 	} else {
		global.alert("<?php echo ACCESSDENIED.'(2)'; ?>");
		result = false;		
 	}
 	return result;
 }	
 
 /**
 * check <task> tag state
 * @param JQueryObject
 * @return bool, alert errorMsg
 */
 function checkState2(task, newState) {
	var req = task.find('req').html();
	if (req == undefined) {
		req = '';
	}
 	return checkState3(newState, req);
 } 
 
 /**
 * check state (accesRight and requed condition)
 * @param string state
 * @param string req condition
 * @return bool, alert errorMsg
 */
 function checkState3(newState, req) {
	var result = true;
	var i;
	var closedTasks;
	if ((newState != 'waiting') && (req != '')) {
		req = req.split(',');
		closedTasks = getClosedTasks();
		for (i=0; i<req.length; i++) {
			if (closedTasks.indexOf(req[i]) < 0) {
				result = false;			
			}
		}
	}
	if (!result) {
		global.alert("<?php echo NOTSTARTING; ?>");	
	}
	return result; 
 }

 function accessRight(task, viewMessage) {
   var result;
 	if ((loggedUser == task.find('img').attr('src')) || (loggedAdmin())) {
		result = true;
	} else {
		result = false;
		if (viewMessage) {
			global.alert("<?php echo ACCESSDENIED; ?>");		
		}
	}	 
	return result;
 }

 function setReadOnly(taskForm) {
 	taskForm.find('#id').attr('disabled','disabled');
 	taskForm.find('#title').attr('disabled','disabled');
 	taskForm.find('#type').attr('disabled','disabled');
 	taskForm.find('#desc').attr('disabled','disabled');
 	taskForm.find('#state').attr('disabled','disabled');
 	taskForm.find('#prior').attr('disabled','disabled');
 	taskForm.find('#req').attr('disabled','disabled');
 	if ((userMember() && (taskForm.find('#assign').val() == 'https://www.gravatar.com/avatar/')) ||
 	    (loggedAdmin()))  {
 	   taskForm.find('#assign').attr('disabled',false);
 	} else {
 	   taskForm.find('#assign').attr('disabled','disabled');
 	}
 	if (userMember()) {
 	 	$('#cancel').html('<?php echo CANCEL; ?>');
 	 	$('#Ok').show();
 	 	$('#cancel').show();
 	} else {
 	 	$('#cancel').html('<?php echo CLOSE; ?>');
 	 	$('#Ok').hide();
 	 	$('#cancel').show();
 	}
 	$('#deltask').hide();
 }<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

 function setWritable(taskForm) {
 	taskForm.find('#title').attr('disabled',false);
 	taskForm.find('#type').attr('disabled',false);
 	taskForm.find('#desc').attr('disabled',false);
 	taskForm.find('#state').attr('disabled',false);
 	taskForm.find('#prior').attr('disabled',false);
 	taskForm.find('#req').attr('disabled',false);
 	taskForm.find('#assign').attr('disabled',false);
 	$('#cancel').html('<?php echo CANCEL; ?>');
 	$('#Ok').show();
 	$('#cancel').show();
 	if (loggedAdmin()) {
		$('#deltask').show();
	} else {
		$('#deltask').hide();
	}	
 }    	  

 function getStateFromTask(task) {    	  
    	  var state = task.parent()[0].nodeName;
    	  if (state == 'WAITING') {
				state = 'waiting';    	  
    	  }
    	  if (state == 'CANSTART') {
				state = 'canStart';    	  
    	  }
    	  if (state == 'ATWORK') {
				state = 'atWork';    	  
    	  }
    	  if (state == 'CANVERIFY') {
				state = 'canVerify';    	  
    	  }
    	  if (state == 'ATVERIFY') {
				state = 'atVerify';    	  
    	  }
    	  if (state == 'CLOSED') {
				state = 'closed';    	  
    	  }
    	  return state;
  }  

  function setTaskEventHandlers() {	 
        if ($('task').draggable != undefined) {
        	$('task').draggable(); 
        }
        $('task').click(function() {
	        var members = $('members').find('member');
	    	var id = this.id;
	    	var task = $('#'+id);
	    	var taskForm = $('#taskForm');
			var state = getStateFromTask(task);	    	  
	     	
	        // copy member into taskFom user selecor'options
	        var i;
	        var s = '<option value="https://www.gravatar.com/avatar/">?</option>';
	        $('#assign').html('');
		    $('#assign').append(s); 		      
	        for (i=0; i < members.length; i++) {
	      	 s = '<option value="'+members[i].getAttribute('avatar')+'">'+
		      		members[i].innerHTML+'</option>';
				 $('#assign').append(s); 		      
	        }
	
	        // load form'fields from <task>	
	    	  this.style.zIndex=1;
	    	  taskForm.find('#id').val(task.find('id').html());
	    	  taskForm.find('#title').val(task.find('title').html());
	    	  taskForm.find('#desc').val(task.find('desc').html().replace('<br>',"\n"));

	    	  taskForm.find('#type').val(task.find('type').attr('class'));
	    	  taskForm.find('#assign').val(task.find('img').attr('avatar'));
	    	  taskForm.find('#req').val(task.find('req').html());
	    	  taskForm.find('#state').val(state);
	    	  oldAssign = task.find('img').attr('avatar');
	    	  if (!accessRight(task, false)) {
	    	  		setReadOnly(taskForm);
	    	  } else {
					setWritable(taskForm);    	  
	    	  }
	    	  if (taskForm.find('#assign').val() == loggedUser) {
					taskForm.find('#state').attr('disabled',false);    	  
	    	  }
			  $('#taskForm').show();
      });
      $('task').mousedown(function(){
      	atDragging = true;
			this.style.zIndex = 99;      
      });
      $('task').mouseup(function(){
      	atDragging = false;
			this.style.zIndex = 1;
      });
      $('task').css('zIndex',1);
  }
  
  function colTranslate() {
    $('waiting').find('h2').html("<?php echo WAITING; ?>");
    $('canStart').find('h2').html("<?php echo CANSTART; ?>");
    $('atWork').find('h2').html("<?php echo ATWORK; ?>");
    $('canVerify').find('h2').html("<?php echo CANVERIFY; ?>");
    $('atVerify').find('h2').html("<?php echo ATVERIFY; ?>");
    $('closed').find('h2').html("<?php echo CLOSED; ?>");
  }
  
  function colResize() {  
    // adjust heights
	var maxHeight = 0;	
    var cols = $('project').find('.col');
    var i;
    var col = null;
    var tasks = [];
    var maxTaskCount = 0;
    for (i=0; i<cols.length; i++) {
    	col = $(cols[i].nodeName);
    	tasks = col.find('task');
    	if (tasks != undefined) {
    		if (tasks.length > maxTaskCount) {
    			maxTaskCount = tasks.length;
    		}
    	}
    }
   	maxHeight = 38 + (maxTaskCount * 122);
    $('.col').css('height', maxHeight+'px');
    $('body').css('height', (maxHeight+200)+'px');
  }
  
  /**
   * task drop
   * @param event
   * @param ui {"offset":{"left":0}, "draggable":jQueryElement}
   * @returns
   */
  function taskDrop(event, ui) {
		// drop into body
		if (ui.draggable.attr('id') == 'popup') {
			return;
		}
	  
		// calculate newState
		var newState;
		if (ui.offset.left > 970) {
			newState = 'closed';
		} else if (ui.offset.left > 776) {
			newState = 'atverify';
		} else if (ui.offset.left > 582) {
			newState = 'canverify';
		} else if (ui.offset.left > 388) {
			newState = 'atwork';
		} else if (ui.offset.left > 194) {
			newState = 'canstart';
		} else {
			newState = 'waiting';
		}
		
		// calculate beforSelector
		var beforeSelector = 'h2';
		//if (ui.offset.top > (scrolTop+60)) {
			var tasks = $(newState).find('task');
			var i;
			for (i=0; i < tasks.length; i++) {
				if (ui.draggable.position().top > $('#'+tasks[i].id).position().top) {
					beforeSelector = '#'+tasks[i].id;				
				}			
			}
		//}

		// check, if ok process
		if ((checkState2(ui.draggable, newState)) && (accessRight(ui.draggable, true))) {
      	 	ui.draggable.insertAfter($(newState).find(beforeSelector));
			dbRefreshEnable = false;
	 		   clearTimeout(saveTimer);
	       	colResize();
	       	// update into database
	       	var s = taskToJson(ui.draggable);
 			dbRefreshEnable = false;
		 	global.post('./app.php', {"option":"tasks", "task":"taskupdate", 
		 		"projectid":projectId, "data": s, "sid": sid, "state": newState}, function(res) {
		 			dbRefreshEnable = true;
		 			if (res.errorMsg != '') {
		 				global.alert(res.errorMsg);
		 			}
		 	});
		}
	 	ui.draggable.css('left','0px');
	 	ui.draggable.css('top','0px');
  }
  
  function pageOnLoad() {	  
    colTranslate(); 
    colResize();
    setTaskEventHandlers();

    if ($('body').droppable != undefined) {
    	$('body').droppable({drop: taskDrop});
    }
    
    $('#Ok').click(function() {
      // taskForm --> task dom element (!!! update parent !!!)
    	var taskForm = $('#taskForm');
    	var id = taskForm.find('#id').val();
    	var task = $('#'+id);
    	var newState = taskForm.find('#state').val();
    	var oldState = getStateFromTask(task);
    	var assign = taskForm.find('#assign').val();
		var assignSelect = taskForm.find('#assign')[0];
		var selectedIndex = assignSelect.selectedIndex;
		var nick = assignSelect.options[selectedIndex].label;

		if (checkForm(taskForm)) { 
	      task.find('title').html(taskForm.find('#title').val());
	      task.find('desc').html(taskForm.find('#desc').val().replace(/\n/g,"<br>"));
	      task.find('type').attr('class', taskForm.find('#type').val());
	      task.find('img').attr('src',assign);
	      task.find('img').attr('avatar',assign);
	      task.find('img').attr('title',nick);
	      task.find('req').html(taskForm.find('#req').val());
		  if (newState != oldState) {	      
				task.insertAfter($(newState).find('h2'));
	      }
	      dbRefreshEnable = false;
 		  clearTimeout(saveTimer);
       	  //saveTimer = window.setTimeout("saveToDatabase(projectId)", 5000);
       	  colResize();
	      // update into database
	      s = taskToJson(task);
		  dbRefreshEnable = false;
		  global.post('./app.php', {"option":"tasks", "task":"taskupdate", 
		 		"projectid": projectId, "data": s, "sid": sid, "state": newState}, function(res) {
		 			dbRefreshEnable = true;
		 			if (res.errorMsg != '') {
		 				global.alert(res.errorMsg);
		 			}
		  });
		}
	 	$('#savedMsg').hide();
		$('#taskForm').hide();  
    });

    $('#cancel').click(function() {
		$('#taskForm').hide();    
    });

    $('#deltask').click(function() {
    	if (loggedAdmin()) {
           var id = $('#taskForm').find('#id').val();
           $('#'+id).remove();
		   dbRefreshEnable = false;
 		   clearTimeout(saveTimer);
	 	   $('#savedMsg').hide();
	 	   $('#taskForm').hide();
		   // delete from database
		   dbRefreshEnable = false;
		   global.post('./app.php', {"option":"tasks", "task":"taskdelete", 
			 		"projectid":projectId, "id": id, "sid": sid}, function(res) {
			 			dbRefreshEnable = true;
			 			if (res.errorMsg != '') {
			 				global.alert(res.errorMsg);
			 			}
		   });
	 	}    
    });
    
    $('#newTaskBtn').click(function() {
    	if (loggedAdmin()) {
	      var id = 1 + getIdMax();
	      var s = '<task id="'+id+'" style="z-index:1">'+
				'<id>'+id+'</id>'+
				'<title></title>'+
				'<desc></desc>'+
				'<type class="question"></type>'+
				'<assign><img src="https://www.gravatar.com/avatar/" avatar="https://www.gravatar.com/avatar/" title="?" alt="" /></assign>'+
				'<req></req>'+
			'</task>';
	      $('waiting h2').after(s);
	      setTaskEventHandlers();
		  window.scrollTo(0,0);
		  dbRefreshEnable = false;
		  clearTimeout(saveTimer);
	      colResize(); 
	      
	      // insert  into database
		  dbRefreshEnable = false;
		  global.post('./app.php', {"option":"tasks", "task":"taskinsert", 
		 		"projectid": projectId, "id": id, "sid": sid}, function(res) {
		 		  dbRefreshEnable = true;
		 		  if (res.errorMsg == '') {
		 			  $('#'+id).click();
		 		  } else {
		 			  global.alert(res.errorMsg);
		 		  }	  
		  });
	      
		}
    }); // newTask

	 $('#membersBtn').click(function() {
	 	var tbody = $('#membersForm tbody');
	 	var s = '';
	 	var i;
	 	var members = $('members').find('member');
 		var checked = '';
	 	tbody.html('');
	 	for (i=0; i < members.length; i++) {
	 		if (members[i].getAttribute('admin') == '1') {
	 			checked = ' checked=\"1\"';
	 		} else {
	 			checked = '';
	 		}
	 		var avatar = members[i].getAttribute('avatar');
	 		if (loggedAdmin() && (i >=
	 			1)) {
				s = '<tr><td><input type="checkbox" id="" value="1"+'+checked+' /></td>'+
				   '<td>'+
				   '<img src="'+avatar+'" alt="'+avatar+'" width="40" height="40" />'+
				   '<span>'+members[i].innerHTML+'</span>'+
				   '</td></tr>';
			} else {
				s = '<tr><td><input type="checkbox" disabled="disabled" id="" value=""'+checked+' /></td>'+
				   '<td>'+
				   '<img src="'+avatar+'" alt="'+avatar+'" width="40" height="40" />'+
				   '<span>'+members[i].innerHTML+'</span>'+
				   '</td></tr>';
			}   
			tbody.append(s);
	 	}
	 	$('#membersForm tr input').click(function() {
		 	var members = $('members').find('member');
		 	var i = 0;
		 	var admin = 0;
		 	// actual avatar
		 	var avatar = this.parentNode.nextSibling.firstChild.alt;
		 	
		 	// i=0 is the  creator it is admin.
			members[0].setAttribute('admin','1');
			for (i=1; i < members.length; i++) {
				if (members[i].getAttribute('avatar') == avatar) {
					if (this.checked) {
						members[i].setAttribute('admin','1');
						admin = 1;
					} else {
						members[i].setAttribute('admin','0');
						admin = 0;
					}				
				}		
			}			 	
 		   clearTimeout(saveTimer);
 		   
 		   // update one member into database
 		   global.post('./app.php', {"option":"members", "task":"memberupdate", 
		 		"projectid":projectId, "avatar": avatar, "admin": admin, "sid": sid}, function(res) {
		 		  dbRefreshEnable = true;
		   });
 		   
 		   
	 	});
		$('#membersForm').toggle();	 
	 });

	if ($(window).unload != undefined) { 
		$(window).unload(function() {
			saveToDatabase(projectId);    
		});
	}
    
	$(window).scroll(function() {
		var scrollTop  = window.pageYOffset || document.documentElement.scrollTop;
		$('.col h2').css('top',(scrollTop)+'px');
	});
	
    // init application
    // ================
	
    // calculate refreshTime
    refreshTime = REFRESHMAX * (SESSIONCOUNT / 100);
    if (refreshTime < REFRESHMIN) {
    	refreshTime = REFRESHMIN;
    }
    if (refreshTime > REFRESHMAX) {
    	refreshTime = REFRESHMAX;
    }
    refreshTime = refreshTime * 1000;
    
    // start refresh interval
	refreshFromDatabase(projectId);
	if ($('#popup').draggable != undefined) {
		$('#popup').draggable();
	}
    if (users.length > 0) {
		   console.log('member load');
		   
		   //copy admins from members dom element into admins array
			var oldMembers = $('members').find('member');

			console.log(oldMembers.length);
			
			var i;
			var s = '';
			for (i=0; i < oldMembers.length; i++) {
				if (oldMembers[i].getAttribute('admin') == '1') {
					admins.push(oldMembers[i].getAttribute('avatar'));			
				}		
			}
			// clear <members>
			$('members').html('');
			
			// copy users into <members> (first user is admin!)
			for (i=0; i < users.length; i++) {
				if ((admins.indexOf(users[i][0]) >= 0) || (i == 0)) {
					s = '<member avatar="'+users[i][0]+'" admin="1">'+users[i][1]+'</member>';
				} else {
					s = '<member avatar="'+users[i][0]+'" admin="0">'+users[i][1]+'</member>';
				}
				$('members').append(s);
			}
		   dbSaveEnable = true;
		   saveAllMembers(projectId);
		   dbRefreshEnable = true;
		   if (loggedAdmin()) {
		   		$('#newTaskBtn').show();
		   } else {
		   		$('#newTaskBtn').hide();
		   }
    	
    }	
	
  } // pageOnLoad function

  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	