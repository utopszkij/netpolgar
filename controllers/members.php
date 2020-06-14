<?php
include_once 'controllers/common.php';
class MembersController extends CommonController {
    
    function __construct() {
        $this->cName = 'members';
    }
    
    protected function getObjectState(string $type, int $objectId): string {
        $result = '';
        $model = $this->getModel($type.'s');
        if ($model) {
            $rec = $model->getRecord($objectId);
            if ($rec) {
                if (isset($rec->state)) {
                    $result = $rec->state;
                }
            }
        }
        return $result;
    }

	/**
	 * members böngésző 
	 * ha userGroupAdmin akkor van "add" és "invite" gomb is, 
	 * @param Request $request type, objectid
	 * -sessionba jöhet: user, offset, order, order_dir, searchstr, filterState, limit
	 */
	public function list(Request $request, $msg = '', $msgClass = '') {
	    $p = $this->init($request,['type','objectid','groups']);
	    if ($msg != '') {
	        $p->msgs = [$msg];
	    }
	    if ($msgClass != '') {
	        $p->msgClass = $msgClass;
	    }
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','group');
	    $p->objectId = $request->input('objectid','0');
	    $p->id = $p->objectId;
	    $p->typeId = $p->type.$p->objectId;
	    $p->memberState = $this->model->getState($p->type, (int)$p->objectId, $p->loggedUser->id); 
	    if ($p->type == 'group') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->objectId);
	        $p->backUrl = MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->objectId.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = ($this->model->getState($p->type, $p->objectId, $p->loggedUser->id) == 'admin');
	        $p->formTitle = $p->group->name.' '.txt('GROUP_MEMBERS');
	    }
	    $p->offset = $request->input('offset', $request->sessionGet($p->typeId.'MembersOffset',0));
	    $p->limit = $request->input('limit', $request->sessionGet($p->typeId.'MembersLimit',20));
	    $p->searchstr = $request->input('searchstr', $request->sessionGet($p->typeId.'MembersSearchstr',''));
	    $p->filterState = $request->input('filterstate', $request->sessionGet($p->typeId.'MembersFilterState',''));
	    $p->order = $request->input('order', $request->sessionGet($p->typeId.'MembersOrder','nick'));
	    $p->order_dir = $request->input('order_dir', $request->sessionGet($p->typeId.'MembersOrder_dir','ASC'));
	    $p->formIcon = 'fa-user';
	    $p->itemTask = 'form';
	    $p->addUrl = ''; // not add button
	    $request->sessionSet($p->typeId.'MembersOffset',$p->offset);
	    $request->sessionSet($p->typeId.'MembersLimit',$p->limit);
	    $request->sessionSet($p->typeId.'MembersOrder',$p->order);
	    $request->sessionSet($p->typeId.'MembersOrder_dir',$p->order_dir);
	    $request->sessionSet($p->typeId.'MembersSearchstr',$p->searchstr);
	    $request->sessionSet($p->typeId.'MembersFilterState',$p->filterState);
	    $p->total = 0;
	    $p->items = $this->model->getRecords($p, $p->total);
	    $this->view->browser($p);
	}
	
	/**
	 * member adatform userGroupAdmin modosithat, törölhet, mások csak nézhetik
	 * @param Request $request - csrtoken, type, objectid, id
	 * session: user, csrToken
	 */
	public function form(Request $request) {
	    $p = $this->init($request,['type', 'objectid', 'id']);
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','group');
	    $p->objectId = $request->input('objectid','0');
	    $p->memberId = $request->input('id',0);
	    $memberRec = $this->model->getRecord($p->memberId);
	    if (!$memberRec) {
	        $memberRec = new MemberRecord();
	    }
	    //$likeModel = $this->getModel('likes');
	    //$p->like = $likeModel->get('members', $p->memberId);
	    $p->like = JSON_decode('{"total":{"up":0, "down":0}, "member":{"up":0, "down":0}}');
	    
	    if ($p->type = 'group') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->objectId);
	        $p->groupAvatar = $p->group->avatar;
	        $p->groupName = $p->group->name;
	        $p->backUrl = MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->objectId.'/'.$p->csrToken.'/1';
	    }
	    $userModel = $this->getModel('users');
	    $p->loggedState = $this->model->getState($p->type, $p->objectid, $p->loggedUser->id);
	    $p->user = $userModel->getById($memberRec->user_id);
	    $p->userState = $this->model->getState($p->type, $p->objectid, $memberRec->user_id);
	    $this->view->form($p);
	}
	
    /**
     * insert new tag state=aspirant
     * @param Request $request - csrToken, type, id, userid  
     */
	public function aspire(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->createCsrToken($request, $p);
	    $url = '';
	    $msgs = $this->model->addMember($p->type, $p->id, $p->userid, 'aspirant');
	    if (count($msgs) == 0) {
	        if ($p->type == 'group') {
	            $url = config('MYDOMAIN').'/opt/groups/groupform/groupid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($p->type == 'project') {
	            $url = config('MYDOMAIN').'/opt/projects/projectform/projectid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($url != '') {
	            redirectTo($url);
	        } else {
	            $this->view->successMsg($msgs,'','',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	}
	
	/**
	 * delete aspirant member record
	 * @param Request $request - csrToken, type, id, userid
	 */
	public function notaspire(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'aspirant') {
	        $msgs = $this->model->deleteMember($p->type, $p->id, $p->userid);
	    } else {
	        $msgs = [txt('NOT_FOUND')];
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	    if (count($msgs) == 0) {
	        if ($p->type == 'group') {
	            $url = config('MYDOMAIN').'/opt/groups/groupform/groupid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($p->type == 'project') {
	            $url = config('MYDOMAIN').'/opt/projects/projectform/projectid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($url != '') {
	            redirectTo($url);
	        } else {
	            $this->view->successMsg($msgs,'','',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	}
	
	/**
	 * member quit from object
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function quit(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'admin') {
	        $msgs = [txt('ADMIN_CAN_NOT_QUIT')];
	        $this->view->errorMsg($msgs,'','',$p);
	        return;
	    }
	    if ($state != 'none') {
	        $msgs = $this->model->deleteMember($p->type, $p->id, $p->userid);
	        $this->model->updateMember($p->type, $p->id, $p->userid, 'exited');
	    } else {
	        $msgs = [txt('NOT_FOUND')];
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	    if (count($msgs) == 0) {
	        if ($p->type == 'group') {
	            $url = config('MYDOMAIN').'/opt/groups/groupform/groupid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($p->type == 'project') {
	            $url = config('MYDOMAIN').'/opt/projects/projectform/projectid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($url != '') {
	            redirectTo($url);
	        } else {
	            $this->view->successMsg($msgs,'','',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	}
	
	/**
	 * member update state active to pause
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function pause(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'active') {
	        $msgs = $this->model->updateMember($p->type, $p->id, $p->userid, 'pause');
	    } else {
	        $msgs = [txt('NOT_FOUND')];
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	    if (count($msgs) == 0) {
	        if ($p->type == 'group') {
	            $url = config('MYDOMAIN').'/opt/groups/groupform/groupid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($p->type == 'project') {
	            $url = config('MYDOMAIN').'/opt/projects/projectform/projectid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($url != '') {
	            redirectTo($url);
	        } else {
	            $this->view->successMsg($msgs,'','',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	}
	
	/**
	 * member update state pause to active
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function active(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'pause') {
	        $msgs = $this->model->updateMember($p->type, $p->id, $p->userid, 'active');
	    } else {
	        $msgs = [txt('NOT_FOUND')];
	        $this->view->errorMsg($msgs,'','',$p);
	    }
	    if (count($msgs) == 0) {
	        if ($p->type == 'group') {
	            $url = config('MYDOMAIN').'/opt/groups/groupform/groupid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($p->type == 'project') {
	            $url = config('MYDOMAIN').'/opt/projects/projectform/projectid/'.$p->id.'/'.$p->csrToken.'/1';
	        }
	        if ($url != '') {
	            redirectTo($url);
	        } else {
	            $this->view->successMsg($msgs,'','',$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','', $p);
	    }
	}
	
    /**
     * update state in members record
     * @param Request $request
     * @param string $oldState
     * @param string $newState
     */
	protected function updateState(Request $request, string $oldState, string $newState) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    if ($this->getObjectState($p->type, $p->id) != 'active') {
	        return;
	    }
	    $this->createCsrToken($request, $p);
	    $msgs = [];
	    $loggedState = $this->model->getState($p->type, $p->objectid, $p->loggedUser->id);
	    // check logged user admin?
	    if ($loggedState != 'admin') {
	        $msgs[] = 'ACCES_VIOLATION';
	    } else {
	        // check loggeduser != user
	        if ($p->loggedUser->id == $p->userid) {
	            $msgs[] = 'SELF_UPDATE_DISABLED';
	        } else {
	            // check user state == $oldState ?
	            $userState = $this->model->getState($p->type, $p->objectid, $p->userid);
	            if ($userState != $oldState) {
	                $msgs[] = 'ACCES_VIOLATION';
	            } else {
	                // execute
	                $msgs = $this->model->updateMember($p->type, $p->objectid, $p->userid, $newState);
	            }
	        }
	    }
	    if (count($msgs) != 0) {
	        $this->view->errorMsg($msgs, '', '', $p);
	    } else {
	        // redirect to member list
	        $this->list($request, txt('STATE_UPDATED'), 'info');
	    }
	}
	
	/**
	 * set meber to state=admin
	 * @param Request $request - type, objectid, userid, loggedUser
	 */
	public function setadmin(Request $request) {
	    $this->updateState($request, 'active', 'admin');
	}
	
	
	/**
	 * set meber to state=active
	 * @param Request $request - type, objectid, userid, loggedUser
	 */
	public function setnotadmin(Request $request) {
	    $this->updateState($request, 'admin', 'active');
	}
}
?>