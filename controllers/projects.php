<?php
include_once 'controllers/common.php';
class ProjectsController extends CommonController {
    
    function __construct() {
        $this->cName = 'projects';
    }
    

	/**
	 * projects böngésző 
	 * ha userGroupAdmin akkor van "add" és "invite" gomb is, 
	 * @param Request $request  - opcionálisan browser paraméterek, userid
	 * -sessionba jöhet: loggedUser, offset, order, order_dir, searchstr, filterState, limit
	 */
	public function list(Request $request, $msgs = [], $msgClass = 'danger') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['id','userid']);
        $p->msgs = $msgs;
        $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN;
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = txt('PROJECTS');
	    if ($p->userid > 0) {
	        $userModel = $this->getModel('users');
	        $p->member = $userModel->getById($p->userid);
	        $p->formTitle .= ' '.txt('IN_MEMBER').' '.$p->member->nick;
	    }
	    $p->formIcon = 'fa-wrench';
	    
        // sztendert browser paraméterek beállítása
	    $p->offset = (int)$request->input('offset', $request->sessionGet('ProjectsOffset',0));
	    if ($p->offset == '') {
	        $p->offset = 0;
	    }
	    $p->limit = (int)$request->input('limit', $request->sessionGet('ProjectsLimit',20));
	    $p->searchstr = $request->input('searchstr', $request->sessionGet('ProjectsSearchstr',''));
	    $p->filterState = $request->input('filterstate', $request->sessionGet('ProjectsFilterState',''));
	    $p->order = $request->input('order', $request->sessionGet('ProjectsOrder','p.name'));
	    if ($p->order == '') {
	        $p->order = 'p.name';
	    }
	    $p->order_dir = $request->input('order_dir', $request->sessionGet('ProjectsOrder_dir','ASC'));
	    $p->itemTask = 'form';
	    $p->addTask = 'add';
	    $p->addUrl = config('MYDOMAIN').'/opt/projects/add';
	    $request->sessionSet('ProjectsOffset',$p->offset);
	    $request->sessionSet('ProjectsLimit',$p->limit);
	    $request->sessionSet('ProjectsOrder',$p->order);
	    $request->sessionSet('ProjectsOrder_dir',$p->order_dir);
	    $request->sessionSet('ProjectsSearchstr',$p->searchstr);
	    $request->sessionSet('ProjectsFilterState',$p->filterState);
	    $p->total = 0;
	    $p->items = $this->model->getProjectRecords($p, $p->total);

	    // items elemek szépítése
	    for ($i=0; $i < count($p->items); $i++) {
	        if ($p->items[$i]->avatar == '') {
	            $p->items[$i]->avatar = './images/noimage.png';
	        }
	    }
	    
        $this->view->browser($p);
	}
	
	/**
	 * browse active projects
	 * @param Request $request
	 */
	public function active(Request $request) {
	    $request->set('filterstate','active');
	    $this->list($request);
	}
	
	/**
	 * browse closed projects
	 * @param Request $request
	 */
	public function closed(Request $request) {
	    $request->set('filterstate','closed');
	    $this->list($request);
	}
	
	/**
	 * browse draft projects
	 * @param Request $request
	 */
	public function draft(Request $request) {
	    $request->set('filterstate','draft');
	    $this->list($request);
	}
	
	/**
	 * project adatform 
	 * @param Request $request - csrtoken, type, id, 
	 * session: user, csrToken
	 */
	public function form(Request $request, array  $msgs=[], string $msgClass='info') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['type', 'type', 'id']);
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN.'/opt/projects/list/'.$p->csrToken.'/1';
	    
	    
	    // item beolvasása, dátumok átalakítása a megjelenítés érdekében
	    $p->item = $this->model->getRecord((int)$p->id); // project record
	    if (!$p->item) {
	        $p->item = new ProjectRecord();
	    }
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = $p->item->name.' '.txt('PROJECT');
	    $p->formIcon = 'fa-wrench';
	    
	    // ha hibaüzenettel lett visszahivva akkor form adatok a requestből
	    foreach ($p->item as $fn => $fv) {
	        $p->item->$fn = $request->input($fn, $fv);
	    }
	    
	    
	    if ($p->item->avatar == '') {
	        $p->item->avatar = './images/noimage.png';
	    }
	    $p->item->deadline = str_replace('.','-',$p->item->deadline); // html -nek yyyy-mm-dd forma kell
	    $likeModel = $this->getModel('likes');
	    $p->likeCount = $likeModel->getCounts('projects', $p->id, $p->loggedUser->id);
	    $messagesModel = $this->getModel('messages');
	    $p->commentsCount = $messagesModel->getCounts('projects', $p->id, $p->loggedUser->id);
	    $p->messagesCount = $messagesModel->getCounts('private', $p->loggedUser->id, $p->loggedUser->id);
	    
	    // !!! NINCS KÉSZ !!!
	    $p->pollCount = JSON_decode('{"total":13, "new":2}'); // aktiv szavazások ahol még nem szavazott, és szavazhat
	    $p->eventCount = JSON_decode('{"total":45, "new":3}'); // jövőbeli események
	    
	    
        $memberModel = $this->getModel('members');
        $p->loggedState = $memberModel->getState($p->type, $p->id, $p->loggedUser->id);
	    $this->view->form($p);
	}
	
	/**
	 * új project felvitele
	 * @param Request $request - sessionban loggedUser
	 */
	public function add(Request $request, array  $msgs=[], string $msgClass='info') {
	    // task inicializálás, feltétlen szükséges request elemek felsorolása
	    $p = $this->init($request,['type', 'type', 'id']);
	    if ($p->loggedUser->id <= 0) {
	        $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	        return;
	    }
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN.'/opt/projects/list/'.$p->csrToken.'/1';
	    
	    // formTitle és form ikon beállítása
	    $p->formTitle = txt('NEW_PROJECT');
	    $p->formIcon = 'fa-wrench';
	    
	    // új item előkészítése
	    $p->item = new ProjectRecord();
	    $p->item->deadline = date('Y-m-d', time() + (10*24*60*60)); // a html -nek ilyen formátum kell!
	    $p->item->avatar = './images/noimage.png';
	    $p->likeCount = JSON_decode('{"up":0, "down":0, "upChecked":false, "downChecked":false}');
	    $p->commentCount = JSON_decode('{"total":0, "new":0}');
	    $p->messageCount = JSON_decode('{"total":0, "new":0}'); 
	    $p->loggedState = 'admin';
	    
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
	 * project form adatainak tárolása (insert vagy update)
	 * @param Request $request - form adatai, sessionban loggedUser
	 */
	public function save(Request $request) {
	    $p = $this->init($request,['type', 'type', 'id']);
	    
	    // csak regisztrált tag vihet fel
	    if ($p->loggedUser->id <= 0) {
	        $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	        return;
	    }
	    
	    // csak projekt adminisztrátor modosíthat
	    
	    if ($p->id > 0) {
	        $memberModel = $this->getModel('members');
	        $p->loggedState = $memberModel->getState('projects', $p->id, $p->loggedUser->id);
	        if ($p->loggedState != 'admin') {
	            $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	            return;
	        }
	    }
	    
	    // rekord kialakítása a $request -ből
	    $project = new ProjectRecord();
        foreach ($project as $fn => $fv) {
           $project->$fn = $request->input($fn);
        }
        
        // dátumok átalakítása html által kivánt formáról a magyar dátum formára
        $project->deadline = str_replace(' ','',$project->deadline);
        $project->deadline = str_replace('-','.',$project->deadline);
       
        // record ellenörzése, ha jó tárolása és browser képernyő hívása,
        // ha nem jó akkor form visszahívás hibaüzenettel
        if ($project->name == '') {
            $p->msgs[] = txt('NAME_REQUESTED'). JSON_encode($project);
        }
        if (($project->state != 'proposal') &
            ($project->state != 'active') &
            ($project->state != 'ended') &
            ($project->state != 'closed') &
            ($project->state != 'draft') &
            ($project->state != 'waiting')
           ) {
               $p->msgs[] = txt('STATE_INVALID').'('.$project->state.')';
         }
         if (!$this->validateDate($project->deadline, 'Y.m.d')) {
             $p->msgs[] = txt('DATE_INVALID').'('.$project->deadline.')';
         }
         if (!is_numeric($project->project_to_active)) {
             $p->msgs[] = txt('INVALID_NUMBER');
         }
         if (!is_numeric($project->project_to_close)) {
             $p->msgs[] = txt('INVALID_NUMBER');
         }
         if (!is_numeric($project->member_to_active)) {
             $p->msgs[] = txt('INVALID_NUMBER');
         }
         if (!is_numeric($project->member_to_exclude)) {
             $p->msgs[] = txt('INVALID_NUMBER');
         }
         
        if (count($p->msgs) == 0) {
            if ($project->id == 0) {
                $this->model->save($project);
                $memberModel = $this->getModel('members'); 
                $memberModel->addMember('projects', $project->id, $p->loggedUser->id, 'admin');
            } else {
                $this->model->save($project);
            }
            if ($this->model->getErrorMsg() == '') {
                $this->list($request, [txt('PROJECT_SAVED')], 'success');
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
	
}
?>