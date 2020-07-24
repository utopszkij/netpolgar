<?php
include_once 'controllers/common.php';
class TasksController extends CommonController {
    
    function __construct() {
        $this->cName = 'tasks';
    }
    

	/**
	 * task böngésző 
	 * @param Request $request  - project_id, opcionálisan member_id
	 * -sessionba jöhet: project_id, loggedUser, offset, order, order_dir, searchstr, filterState, limit
	 */
	public function list(Request $request, $msgs = [], $msgClass = 'danger') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['id','project_id']);
	    $p->project_id = $request->input('project_id', $request->sessionGet('TaskProject_id',0));
	    $projectModel = $this->getModel('projects');
	    $p->project = $projectModel->getRecord($p->project_id);
	    $p->msgs = $msgs;
        $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN;
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = txt('TASKS');
	    $p->formIcon = 'fa-wrench';
	    
        // sztendert browser paraméterek beállítása
	    $p->offset = (int)$request->input('offset', $request->sessionGet('TasksOffset',0));
	    if ($p->offset == '') {
	        $p->offset = 0;
	    }
	    $p->limit = (int)$request->input('limit', $request->sessionGet('TasksLimit',20));
	    $p->searchstr = $request->input('searchstr', $request->sessionGet('TasksSearchstr',''));
	    $p->filterState = $request->input('filterstate', $request->sessionGet('TasksFilterState',''));
	    $p->order = $request->input('order', $request->sessionGet('TasksOrder','t.state,t.sequence'));
	    if ($p->order == '') {
	        $p->order = 't.state,t.sequence';
	    }
	    $p->order_dir = $request->input('order_dir', $request->sessionGet('TasksOrder_dir','ASC'));
	    $p->itemTask = 'form';
	    $p->addTask = 'add';
	    $p->addUrl = config('MYDOMAIN').'/opt/tasks/add/project_id/'.$p->project_id;
	    $request->sessionSet('TaskProject_id',$p->project_id);
	    $request->sessionSet('TasksOffset',$p->offset);
	    $request->sessionSet('TasksLimit',$p->limit);
	    $request->sessionSet('TasksOrder',$p->order);
	    $request->sessionSet('TasksOrder_dir',$p->order_dir);
	    $request->sessionSet('TasksSearchstr',$p->searchstr);
	    $request->sessionSet('TasksFilterState',$p->filterState);
	    $p->total = 0;
	    $p->items = $this->model->getTasksRecords($p, $p->total);

	    $memberModel = $this->getModel('members');
	    $userModel = $this->getModel('users');
	    $p->loggedState = $memberModel->getState('projects', $p->project_id, $p->loggedUser->id);
	    
	    // items elemek szépítése
	    // szin állítás state alapján, user avatar nick alapján
	    for ($i = 0; $i < count($p->items);  $i++) {
	        $p->items[$i]->description = str_replace("\n", " ", $p->items[$i]->description);
	        $p->items[$i]->description = str_replace("\r", " ", $p->items[$i]->description);
	        if (strlen($p->items[$i]->description) > 60) {
	            $p->items[$i]->description = substr($p->items[$i]->description,0,60).'...';
	        }
	        if ($p->items[$i]->nick != '') {
	            $user = $userModel->getByNick($p->items[$i]->nick);
	            $p->items[$i]->avatar = $user->avatar;
	            if ($p->items[$i]->avatar == '') {
	                $p->items[$i]->avatar = 'images/noavatar.png';
	            }
	        } else {
	            $p->items[$i]->avatar = '';
	        }
	    }
        $this->view->browser($p);
	}
	
	/**
	 * taskt adatform 
	 * @param Request $request - csrtoken, type, id, 
	 * session: user, csrToken
	 */
	public function form(Request $request, array  $msgs=[], string $msgClass='info') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['tasktype', 'description', 'id']);
	    $p->project_id = $request->input('project_id', $request->sessionGet('TaskProject_id',0));
	    $projectModel = $this->getModel('projects');
	    $p->project = $projectModel->getRecord($p->project_id);
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN.'/opt/tasks/list/'.$p->csrToken.'/1';
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = txt('TASK');
	    $p->formIcon = 'fa-wrench';
	    
	    // item beolvasása, dátumok átalakítása a megjelenítés érdekében
	    $p->item = $this->model->getRecord((int)$p->id); // task record
	    if (!$p->item) {
	        $p->item = new TaskRecord();
	    }
	    
	    // ha hibaüzenettel lett visszahivva akkor form adatok a requestből
	    foreach ($p->item as $fn => $fv) {
	        $p->item->$fn = $request->input($fn, $fv);
	    }
	    
	    $p->item->deadline = str_replace('.','-',$p->item->deadline); // html -nek yyyy-mm-dd forma kell
	    $memberModel = $this->getModel('members');
	    $p->loggedState = $memberModel->getState('projects', $p->project_id, $p->loggedUser->id);
	    $p->item->project_id = $p->project_id;
	    $this->view->form($p);
	}
	
	/**
	 * új task felvitele
	 * @param Request $request - sessionban loggedUser
	 */
	public function add(Request $request, array  $msgs=[], string $msgClass='info') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['type', 'type', 'id']);
	    $p->project_id = $request->input('project_id', $request->sessionGet('TaskProject_id',0));
	    $projectModel = $this->getModel('projects');
	    $p->project = $projectModel->getRecord($p->project_id);
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN.'/opt/tasks/list/'.$p->csrToken.'/1';
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = txt('NEW_TASK');
	    $p->formIcon = 'fa-wrench';
	    
	    $memberModel = $this->getModel('members');
	    $p->loggedState = $memberModel->getState('projects', $p->project_id, $p->loggedUser->id);
	    
	    // új item előkészítése
	    $p->item = new TaskRecord();
	    $p->item->deadline = date('Y-m-d', time() + (10*24*60*60)); // a html -nek ilyen formátum kell!
	    $p->item->state = 'wait_run';
	    $p->item->tasktype = 'task';
	    $p->item->project_id = $p->project_id;
	    
	    // ha hibaüzenettel lett visszahivva akkor form adatok a requestből
	    foreach ($p->item as $fn => $fv) {
	        $p->item->$fn = $request->input($fn, $fv);
	    }
	    
	    $this->view->form($p);
	}
	
	protected function validateDate($date, $format = 'Y-m-d') {
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
	
	/**
	 *  ha most inditjuk akkor az inditási feltételek fentállnak?
	 * @param TaskRecord $task
	 * @return string
	 */
	protected function checkRun($task): string {
	    $result = '';
	    if ($task->state == 'runing') {
	        if ($task->reqclosed != '') {
	            $w = $this->model->getRecord($task->reqclosed);
	            if ($w) {
	                if (($w->project_id == $task->project_id) & ($w->state != 'closed')) {
	                    $result = txt('CAN_NOT_START').' '.$task->reqclosed.' ';
	                }
	            }
	        }
	        if ($task->reqnotrun != '') {
	            $w = $this->model->getRecord($task->reqnotrun);
	            if ($w) {
	                if (($w->project_id == $task->project_id) & ($w->state == 'runing')) {
	                    $result .= txt('CAN_NOT_START').' '.$task->reqnotrun;
	                }
	            }
	        }
	    } // inditjuk
	    return $result;
	}
	
	/**
	 * task form adatainak tárolása (insert vagy update)
	 * @param Request $request - form adatai, sessionban loggedUser
	 */
	public function save(Request $request) {
	    $p = $this->init($request,['tasktype', 'description', 'id', 'project_id']);
	    $memberModel = $this->getModel('members');
	    $userModel = $this->getModel('users');
	    $p->loggedState = $memberModel->getState('projects', $p->project_id, $p->loggedUser->id);
	    
	    // rekord kialakítása a $request -ből
	    $task = new TaskRecord();
	    foreach ($task as $fn => $fv) {
	        $task->$fn = $request->input($fn);
	    }
	    
	    // régi rekord beolvasása
	    if ($task->id > 0) {
	        $oldTask = $this->model->getRecord($task->id);
	    } else {
	        $oldTask = new TaskRecord();
	    }
	    
	    // csak projekt adminisztrátor modosíthat tetszés szerint
	    // project tag:  -még szavad taskokat magához vehet
	    //               -már magához tartozó taskot modosíthat
	    if (($task->id > 0) & ($p->loggedState != 'admin')) {
	        if (($oldTask->nick != '') |
	            ($oldTask != $p->loggedUser->nick) |
	            ($task != $p->loggedUser->nick)) {
	                $p->msgs[] = txt('ACCESS_VIOLATION');
	            }
	    }
	    
	    // új felvételnés state, sequence fix, 
	    // nick -et admin tetszőlegesen adhat, más csak ajátmagát adhatja meg
        if ($task->id <= 0) {
            $task->state = 'wait_run';
            $task->sequence = 0;
            if (($p->loggedState != 'admin') &
                ($task->nick != '') &
                ($task->nick != $p->loggedUser->nick)) {
                    $p->msgs[] = txt('ACCESS_VIOLATION');
            }
            
            // aki nem projekt tag az csak "bug", "suggestion", "request"  tipust vihet fel
            if (($p->loggedState != 'active') & ($p->loggedState != 'admin')) {
                if (($task->tasktype != 'bug') & ($task->tasktype != 'suggestion') & ($task->tasktype != 'request')) {
                    $p->msgs[] = txt('ACCESS_VIOLATION');
                }
            }
        }
        
        // aki nem projekt tag az nem modosithat
        if (($p->loggedState == 'none') & ($task->id > 0))  {
            $p->msgs[] = txt('ACCESS_VIOLATION');
        }
        
        // dátumok átalakítása html által kivánt formáról a magyar dátum formára
        $task->deadline = str_replace(' ','',$task->deadline);
        $task->deadline = str_replace('-','.',$task->deadline);
       
        // record ellenörzése, ha jó tárolása és browser képernyő hívása,
        // ha nem jó akkor form visszahívás hibaüzenettel
        if ($task->description == '') {
            $p->msgs[] = txt('DESCRIPTION_REQUESTED');
        }
       
        // nick ellenörzése: létező projecttag vagy üres?
        if ($task->nick != '') {
            $user = $userModel->getByNick($task->nick);
            $nickState = $memberModel->getState('projects', $p->project_id, $user->id);
            if (($nickState != 'active') & ($nickState != 'admin')) {
                $p->msgs[] = txt('NICK_INVALID').' '.$task->nick;
            }
        }
        
        // reqclosed létező projekt task vagy üres ?
        if ($task->reqclosed != '') {
            $w = $this->model->getRecord($task->reqclosed);
            if ($w) {
                if ($w->project_id != $p->project_id) {
                    $p->msgs[] = txt('REQ_INVALID').' '.$task->reqclosed;
                }
            } else {
                $p->msgs[] = txt('REQ_INVALID').' '.$task->reqclosed;
            }
        }
        
        // reqnotrun létező project task vagy üres ?
        if ($task->reqnotrun != '') {
            $w = $this->model->getRecord($task->reqnotrun);
            if ($w) {
                if ($w->project_id != $p->project_id) {
                    $p->msgs[] = txt('REQ_INVALID').' '.$task->reqnotrun;
                }
            } else {
                $p->msgs[] = txt('REQ_INVALID').' '.$task->reqnotrun;
            }
        }
        
        $s = $this->checkRun($task);
        if ($s != '') {
            $p->msgs[] = $s;
        }
        
        // state valid?
        if (($task->state != 'wait_req') &
            ($task->state != 'wait_run') &
            ($task->state != 'runing') &
            ($task->state != 'wait_control') &
            ($task->state != 'closed')
            ) {
                $p->msgs[] = txt('STATE_INVALID').'('.$task->state.')';
            }
            
         // tasktype valid?
         if (($task->tasktype != 'task') &
                ($task->tasktype != 'bug') &
                ($task->tasktype != 'request') &
                ($task->tasktype != 'suggestion') &
                ($task->tasktype != 'comment')
                ) {
                    $p->msgs[] = txt('TASKTYPE_INVALID').'('.$task->state.')';
         }
                
         if (!$this->validateDate($task->deadline, 'Y.m.d')) {
             $p->msgs[] = txt('DATE_INVALID').'('.$task->deadline.')';
         }
        if (count($p->msgs) == 0) {
            if ($task->id == 0) {
                $this->model->save($task);
            } else {
                $this->model->save($task);
            }
            if ($this->model->getErrorMsg() == '') {
                $this->list($request, [txt('TASK_SAVED')], 'success');
            } else {
                $this->view->errorMsg([$this->model->getErrorMsg()]);
            }
        } else {
            if ($p->id == 0) {
                $this->add($request, $p->msgs, 'danger');
            } else {
                $this->form($request, $p->msgs, 'danger');
            }
        }
        
	} // save function
	
	/**
	 * AJAX backend move task
	 * @param Request $request - id, state, sequence
     * sequence: 0 - 0-val kell felvinni
     *           max - a meglévő utolsó sequence + 1 -el kell felvinni
     *           >0 - +1 el kell felvinni
	 * @return void  - echo 'OK' vagy hibaüzenet
	 */
	public function newstate(Request $request) {
	    $p = $this->init($request,['id', 'state', 'sequence']);
	    $task = $this->model->getRecord($p->id);
	    
	    // csak projekt tag mozgathat
	    $memberModel = $this->getModel('members');
	    $p->loggedState = $memberModel->getState('projects', $task->project_id, $p->loggedUser->id);
	    if (($p->loggedState != 'admin') & ($p->loggedState != 'active')) {
	        echo txt('ACCES_VIOLATION');
	        return;
	    }
	    
	    // nem admin csak a sajátját mozgathatja
	    if (($p->loggedState != 'admin') & ($task->nick != $p->loggedUser->nick)) {
	        echo txt('ACCES_VIOLATION');
	        return;
	    }
	    
	    $task->state = $p->state;
	    if ($p->sequence == 'first') {
	        $task->sequence = 0;
	    } else if ($p->sequence == 'max') {
	        $lastTask = $this->model->getLast($task->project_id, $task->state);
	        $task->sequence = 1 + $lastTask->sequence;
	    } else {
	        $task->sequence = 1 + $p->sequence;
	    }
	    if ($task) {
	        $s = $this->checkRun($task);
	        if ($s != '') {
	            echo '! '.$s; // ez jelzi, hogy nem inditható
	        } else if ($this->model->save($task)) {
	            echo 'OK';
	        } else {
	            echo $this->model->getErrorMsg();
	        }
	    } else {
	        echo 'not found '.$p->id;
	    }
	    return;
	}
	
	/**
	 * egy task törlése
	 * @param Request $request id
	 */
	public function delete(Request $request) {
	    $p = $this->init($request,['id']);
	    $task = $this->model->getRecord((int)$p->id);
	    if ($task) {
	       $memberModel = $this->getModel('members');
	       $p->loggedState = $memberModel->getState('projects', $task->project_id, $p->loggedUser->id);
	       if ($p->loggedState != 'admin') {
	           $this->list($request, [txt('ACCES_VIOLATION')]);
	       } else {
	           $this->model->delete((int)$p->id);
	           $request->set('id','');
	           $this->list($request, [txt('DELETED')]);
	       }
	    } else {
	       $this->list($request, []);
	    }
	}
}
?>