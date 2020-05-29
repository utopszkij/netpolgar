<?php
include_once 'controllers/common.php';
class MembersController extends CommonController {
	

	/**
	 * members böngésző 
	 * ha userGroupAdmin akkor van "add" és "invite" gomb is, 
	 * @param Request $request type, objectid
	 * -sessionba jöhet: user, offset, order, order_dir, searchstr, limit
	 */
	public function list(Request $request) {
	    $p = $this->init($request,['type','objectid','groups']);
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','group');
	    $p->objectId = $request->input('objectid','0');
	    $p->typeId = $p->type.$p->objectId;
	    $p->memberState = $this->model->getState($p->type, $p->loggedUser->id); 
	    if ($p->type == 'group') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->objectId);
	        $p->backUrl = MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->objectId.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = $this->model->isUserAdmin($p->type, $p->objectId, $p->loggedUser->id);
	        $p->formTitle = txt('GROUP_MEMBERS');
	    }
	    $p->offset = $request->input('offset', $request->sessionGet($p->typeId.'MembersOffset',0));
	    $p->limit = $request->input('limit', $request->sessionGet($p->typeId.'MembersLimit',20));
	    $p->searchstr = $request->input('searchstr', $request->sessionGet($p->typeId.'MembersSearchstr',''));
	    $p->order = $request->input('order', $request->sessionGet($p->typeId.'MembersOrder','nick'));
	    $p->order_dir = $request->input('order_dir', $request->sessionGet($p->typeId.'MembersOrder_dir','ASC'));
	    $p->formIcon = 'fa-user';
	    $p->itemTask = 'form';
	    $p->addUrl = config('MYDOMAIN').'/opt/members/add';
	    $request->sessionSet($p->typeId.'MembersOffset',$p->offset);
	    $request->sessionSet($p->typeId.'MembersLimit',$p->limit);
	    $request->sessionSet($p->typeId.'MembersOrder',$p->order);
	    $request->sessionSet($p->typeId.'MembersOrder_dir',$p->order_dir);
	    $request->sessionSet($p->typeId.'MembersSearchstr',$p->searchstr);
	    $p->total = 0;
	    $p->items = $this->model->getRecords($p, $p->total);
	        
	    // váltakozó trClass beállítás /bootstrap table-striped nem müködik :( /
	    $trClass = 'tr0';
	    foreach ($p->items as $item) {
	        $item->trClass = $trClass;
	        if ($trClass == 'tr0') {
	            $trClass = 'tr1';
	        } else {
	            $trClass = 'tr0';
	        }
	    }
	    $this->view->browser($p);
	}
	
	/**
	 * member adatform userGroupAdmin modosithat, törölhet, mások csak nézhetik
	 * @param Request $request - csrtoken, type, objectid, memberid
	 * session: user, csrToken
	 */
	public function form(Request $request) {
	    $p = $this->init($request,['type', 'objectid', 'memberid', 'groups']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','group');
	    $p->objectId = $request->input('objectid','0');
	    $p->memberId = $request->input('memberid',0);
	    if ($p->type = 'group') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->objectId);
	        $p->backUrl = MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->objectId.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = $this->model->isUserGroupAdmin($p->user, $p->userAdmin, 'group');
	        $p->formTitle = txt('GROUP_MEMBER');
	        $userModel = getModel('users');
	        $p->member = $userModel->getRecord($p->memberId);
	    }
	    $this->view->form($p);
	}
	
    /**
     * insert new tag state=aspire
     * @param Request $request - csrToken, type, id, userid  
     */
	public function aspire(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $url = '';
	    $msgs = $this->model->addMember($p->type, $p->id, $p->userid, 'aspire');
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
	            $this->view->successMsg($msgs,'','',true,$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	}
	
	/**
	 * delete aspre meber record
	 * @param Request $request - csrToken, type, id, userid
	 */
	public function notaspire(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'aspire') {
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
	            $this->view->successMsg($msgs,'','',true,$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	}
	
	/**
	 * member quit from object
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function quit(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'asmin') {
	        $msgs = [txt('ADMIN_CAN_NOT_QUIT')];
	        $this->view->errorMsg($msgs,'','',true,$p);
	        return;
	    }
	    if ($state != 'none') {
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
	            $this->view->successMsg($msgs,'','',true,$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	}
	
	/**
	 * member update state active to pause
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function pause(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'active') {
	        $msgs = $this->model->updateMember($p->type, $p->id, $p->userid, 'pause');
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
	            $this->view->successMsg($msgs,'','',true,$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	}
	
	/**
	 * member update state pause to active
	 * @param Request $request - csrToke, type, id, userid
	 */
	public function active(Request $request) {
	    $p = $this->init($request,['type', 'id', 'userid']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $state = $this->model->getState($p->type, $p->id, $p->userid);
	    if ($state == 'pause') {
	        $msgs = $this->model->updateMember($p->type, $p->id, $p->userid, 'active');
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
	            $this->view->successMsg($msgs,'','',true,$p);
	        }
	    } else {
	        $this->view->errorMsg($msgs,'','',true,$p);
	    }
	}
	
}
?>