<?php
include_once 'controllers/common.php';
class ProjectsController extends CommonController {
    
    function __construct() {
        $this->cName = 'projects';
    }
    

	/**
	 * projects böngésző 
	 * ha userGroupAdmin akkor van "add" és "invite" gomb is, 
	 * @param Request $request  - opcionálisan member_id
	 * -sessionba jöhet: loggedUser, offset, order, order_dir, searchstr, filterState, limit
	 */
	public function list(Request $request, $msg = '', $msgClass = '') {
	    $p = $this->init($request,['id','member_id']);
	    if ($msg != '') {
	        $p->msgs = [$msg];
	    }
	    if ($msgClass != '') {
	        $p->msgClass = $msgClass;
	    }
	    $this->createCsrToken($request, $p);
	    $p->backUrl = MYDOMAIN;
        $p->formTitle = txt('PROJECTS');
        if ($p->member_id != '') {
            $userModel = $this->getModel('users');
            $p->member = $userModel->getRecord($p->member_id);
            $p->formTitle .= ' '.txt('IN_MEMBER').' '.$p->member->nick;
        }
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
	    $p->formIcon = 'fa-user';
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
	    $p = $this->init($request,['type', 'type', 'id']);
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->item = $this->model->getRecord((int)$p->id); // project record
	    if (!$p->item) {
	        $p->item = new ProjectRecord();
	    }
	    $p->formTitle = txt('PROJECT');
//	    $likeModel = $this->getModel('likes');
//	    $p->likeCount = $likeModel->getCounts('projects', $p->memberId, $p->loggedUser->id);
// !!! NINCS KÉSZ !!!
	    $p->likeCount = JSON_decode('{"up":12, "down":3, "upChecked":false, "downChecked":false}');
	    $p->commentCount = JSON_decode('{"total":12, "new":3}');
	    $p->messageCount = JSON_decode('{"total":22, "new":2}'); // olvasatlan privát üzenetek
	    
        $p->backUrl = MYDOMAIN.'/opt/projects/list/'.$p->csrToken.'/1';
        $memberModel = $this->getModel('members');
        $p->loggedState = $memberModel->getState($p->type, $p->id, $p->loggedUser->id);
	    $this->view->form($p);
	}
	
	/**
	 * új project felvitele
	 * @param Request $request - sessionban loggedUser
	 */
	public function add(Request $request, array  $msgs=[], string $msgClass='info') {
	    $p = $this->init($request,['type', 'type', 'id']);
	    if ($p->loggedUser->id <= 0) {
	        $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	        return;
	    }
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->item = new ProjectRecord();
	    $p->formTitle = txt('NEW_PROJECT');
	    $p->likeCount = JSON_decode('{"up":0, "down":0, "upChecked":false, "downChecked":false}');
	    $p->commentCount = JSON_decode('{"total":0, "new":0}');
	    $p->messageCount = JSON_decode('{"total":0, "new":0}'); 
	    $p->backUrl = MYDOMAIN.'/opt/projects/list/'.$p->csrToken.'/1';
	    $p->loggedState = 'admin';
	    $this->view->form($p);
	}
	
	/**
	 * project form adatainak tárolása (insert vagy update)
	 * @param Request $request - form adatai, sessionban loggedUser
	 */
	public function save(Request $request) {
	    $p = $this->init($request,['type', 'type', 'id']);
	    if ($p->loggedUser->id <= 0) {
	        $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	        return;
	    }
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    if ($p->id > 0) {
	        $memberModel = $this->getModel('members');
	        $p->loggedState = $memberModel->getState($p->type, $p->id, $p->loggedUser->id);
	        if ($p->loggedState != 'asmin') {
	            $this->view->errorMsg([txt('ACCESS_VIOLATION')]);
	            return;
	        }
	    }
	    $project = new ProjectRecord();
        foreach ($project as $fn => $fv) {
            if (isset($p->$fn)) {
                $project->$fn = $p->$fn;
            }
        }
        // check record
        if ($p->name == 0) {
            $p->msgs[] = txt('NAME_REQUESTED');
        }
        if (count($p->msgs) == 0) {
            if ($this->model->save($project)) {
                $this->list($request, txt('PROJECT_SAVED'), 'success');
            } else {
                $this->errorMsg([$this->model->getErrorMsg()]);
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