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
	    $p = $this->init($request,['type','id','member_id']);
	    if ($msg != '') {
	        $p->msgs = [$msg];
	    }
	    if ($msgClass != '') {
	        $p->msgClass = $msgClass;
	    }
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','groups');
	    $p->typeId = $p->type.$p->id;
	    $p->memberState = $this->model->getState((string)$p->type, (int)$p->id, (int)$p->loggedUser->id); 
	    if ($p->type == 'groups') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->id);
	        $p->backUrl = MYDOMAIN.'/opt/groups/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = ($this->model->getState($p->type, $p->id, $p->loggedUser->id) == 'admin');
	        $p->formTitle = '"'.$p->group->name.'" '.txt('GROUP_MEMBERS');
	    }
	    if ($p->type == 'projects') {
	        $projectModel = $this->getModel('projects');
	        $p->project = $projectModel->getRecord($p->id);
	        $p->backUrl = MYDOMAIN.'/opt/projects/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = ($this->model->getState($p->type, $p->id, $p->loggedUser->id) == 'admin');
	        $p->formTitle = '"'.$p->project->name.'" '.txt('PROJECT_MEMBERS');
	    }
	    $p->offset = (int)$request->input('offset', $request->sessionGet($p->typeId.'MembersOffset',0));
	    if ($p->offset == '') {
	        $p->offset = 0;
	    }
	    $p->limit = (int)$request->input('limit', $request->sessionGet($p->typeId.'MembersLimit',20));
	    $p->searchstr = $request->input('searchstr', $request->sessionGet($p->typeId.'MembersSearchstr',''));
	    $p->filterState = $request->input('filterstate', $request->sessionGet($p->typeId.'MembersFilterState',''));
	    $p->order = $request->input('order', $request->sessionGet($p->typeId.'MembersOrder','u.nick'));
	    if ($p->order == '') {
	        $p->order = 'u.nick';
	    }
	    $p->order_dir = $request->input('order_dir', $request->sessionGet($p->typeId.'MembersOrder_dir','ASC'));
	    $p->formIcon = 'fa-user';
	    $p->itemTask = 'form';
	    $p->addTask = 'add';
	    $p->addUrl = ''; // not add button
	    $request->sessionSet($p->typeId.'MembersOffset',$p->offset);
	    $request->sessionSet($p->typeId.'MembersLimit',$p->limit);
	    $request->sessionSet($p->typeId.'MembersOrder',$p->order);
	    $request->sessionSet($p->typeId.'MembersOrder_dir',$p->order_dir);
	    $request->sessionSet($p->typeId.'MembersSearchstr',$p->searchstr);
	    $request->sessionSet($p->typeId.'MembersFilterState',$p->filterState);
	    $p->total = 0;
	    $p->items = $this->model->getMemberRecords($p, $p->total);
        $this->view->browser($p);
	}
	
	/**
	 * member adatform userGroupAdmin modosithat, törölhet, mások csak nézhetik
	 * @param Request $request - csrtoken, type, id, id, member_id
	 * session: user, csrToken
	 */
	public function form(Request $request, array  $msgs=[], string $msgClass='info') {
	    $p = $this->init($request,['type', 'type', 'id', 'member_id']);
	    $p->msgs = $msgs;
	    $p->msgClass = $msgClass;
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','groups');
	    $p->objectId = $request->input('id','0'); // group vagy project id
	    $p->memberId = $request->input('member_id',0); // member record id
	    $p->item = $this->model->getRecord((int)$p->memberId); // memebers record
	    if (!$p->item) {
	        $p->item = new MemberRecord();
	    }
	    $likeModel = $this->getModel('likes');
	    $p->likeCount = $likeModel->getCounts('members', $p->memberId, $p->loggedUser->id);
// !!! NINCS KÉSZ !!!
	    $p->commentCount = JSON_decode('{"total":12, "new":3}');
	    $p->messageCount = JSON_decode('{"total":22, "new":2}'); // olvasatlan privát üzenetek
	    
	    if ($p->type = 'groups') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->id);
	        $p->groupAvatar = $p->group->avatar;
	        $p->groupName = $p->group->name;
	        $p->backUrl = MYDOMAIN.'/opt/groups/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	    }
	    $userModel = $this->getModel('users');
	    $p->loggedState = $this->model->getState($p->type, $p->id, $p->loggedUser->id);
	    $p->user = $userModel->getById($p->item->user_id);
	    $p->userState = $this->model->getState($p->type, $p->objectId, $p->user->id);
	    $this->view->form($p);
	}
	
	/**
	 * set user to admin
	 * @param Request $request - type, id, member_id, csrToken, sessionban loggedUser, csrToken
	 */
	public function setadmin(Request $request) {
	    $p = $this->init($request, ['type','id','member_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = [];
        $memberRec = $this->model->getRecord($p->member_id);
        $p->loggedState = $this->model->getState($p->type, $p->id, $p->loggedUser->id);
        if ($memberRec) {
            if (($memberRec->state == 'active') & 
                ($p->loggedState == 'admin') &
                ($memberRec->user_id != $p->loggedUser->id)) {
                $memberRec->state = 'admin';
                if (!$this->model->save($memberRec)) {
                    $msgs = [$this->model->getErrorMsg()];
                }
                } else {
                 $msgs[] = txt('ACCESS_VIOLATION');
            }
        } else {
            $msgs[] = txt('NOT_FOUND');
        }
        if (count($msgs) == 0) {
            $this->form($request,[txt('DATA_SAVED')],'success');
        } else {
            $this->form($request,$msgs,'danger');
        }
	}
	
	/**
	 * set user state admin to active
	 * @param Request $request - type, id, user_id, csrToken, sessionban loggedUser, csrToken
	 */
	public function setnoadmin(Request $request) {
	    $p = $this->init($request, ['type','id','member_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = [];
	    $memberRec = $this->model->getRecord($p->member_id);
	    $p->loggedState = $this->model->getState($p->type, $p->id, $p->loggedUser->id);
	    if ($memberRec) {
	        if (($memberRec->state == 'admin') &
	            ($p->loggedState == 'admin') &
	            ($memberRec->user_id != $p->loggedUser->id)) {
	                $memberRec->state = 'active';
	                if (!$this->model->save($memberRec)) {
	                    $msgs = [$this->model->getErrorMsg()];
	                }
	            } else {
	                $msgs[] = txt('ACCESS_VIOLATION');
	            }
	    } else {
	        $msgs[] = txt('NOT_FOUND');
	    }
	    if (count($msgs) == 0) {
	        $this->form($request,[txt('DATA_SAVED')],'success');
	    } else {
	        $this->form($request,$msgs,'danger');
	    }
	}
	
	/**
	 * tag státusz modosítása csak a logged user saját magát modosíthatja.
	 * ha $newState == 'none' akkor meglévő member rekord törlése
	 * ha $oldState == 'none' akkor új member rekord felvitele
	 * egyébként meglévő member rekord modosítása
	 * @param Params $p - type,id , user_id, loggedUser
	 * @param string $oldState
	 * @param string $newStatet
	 * @return array
	 */
	public function updateMemberState(Params $p,
	    string $oldState, string $newStatet): array {
	    $msgs = [];
	    if (($p->loggedUser->id > 0) & ($p->loggedUser->id == $p->user_id)) {
	        $objectModel = $this->getModel($p->type);
	        if ($objectModel) {
	            $object = $objectModel->getRecord($p->id);
	            if ($object) {
	                $memberRec = $this->model->getRecordBy($p->type, $p->id, $p->user_id);
	                if ($memberRec->state == $oldState) {
	                    $memberRec->type = $p->type;
	                    $memberRec->objectid = $p->id;
	                    $memberRec->user_id = $p->user_id;
	                    $memberRec->state = $newState;
	                    if ($newState == 'none') {
	                        if (!$this->model->delete($memberRec->id)) {
	                            $msgs[] = $this->model->getErrorMsg();
	                        }
	                    } else {
	                        if (!$this->model->save($memberRec)) {
	                            $msgs[] = $this->model->getErrorMsg();
	                        }
	                    }
	                } else {
	                    $msgs[] = txt('ACCESS_VIOLATION').' (2)';
	                }
	            } else {
	                $msgs[] = $p->type.' '.$p->id.' '.txt('NOT_FOUND');
	            }
	        } else {
	            echo 'Fatal error model not fount '.$p->type; exit();
	        }
	    } else {
	        $msgs[] = txt('ACCESS_VIOLATION').' (1)';
	    }
	    return $msgs;
	}
	
	/**
	 * új jelentkező egy csoportba vagy projektbe
	 * @param Request $request - type, id, user_id, csrToken
	 */
	public function aspire(Request $request) {
	    $p = $this->init($request,['type','id','user_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = $this->updateMemberState($p, 'none', 'aspire');
	    if (count($msgs) == 0) {
	        $url = config('MYDOMAIN').'/opt/'.$p->type.'/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $this->redirectTo($url);
	    } else {
	        $this->view->errorMsg($msgs, $url, txt('BACK'), $p);
	    }
	}

	/**
	 * tag kilép egy csoportból vagy projektből
	 * @param Request $request - type, id, user_id, csrToken
	 */
	public function quit(Request $request) {
	    $p = $this->init($request,['type','id','user_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = $this->updateMemberState($p, 'active', 'none');
	    if (count($msgs) == 0) {
	        $url = config('MYDOMAIN').'/opt/'.$p->type.'/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $this->redirectTo($url);
	    } else {
	        $this->view->errorMsg($msgs, $url, txt('BACK'), $p);
	    }
	}
	
	/**
	 * jelentkező visszavonja jelentkezését egy csoportból vagy projektből
	 * @param Request $request - type, id, user_id, csrToken
	 */
	public function notaspire(Request $request) {
	    $p = $this->init($request,['type','id','user_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = $this->updateMemberState($p, 'aspire', 'none');
	    if (count($msgs) == 0) {
	        $url = config('MYDOMAIN').'/opt/'.$p->type.'/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $this->redirectTo($url);
	    } else {
	        $this->view->errorMsg($msgs, $url, txt('BACK'), $p);
	    }
	}
	
	/**
	 * tag szünetelteti a tagságát
	 * @param Request $request - type, id, user_id, csrToken
	 */
	public function pause(Request $request) {
	    $p = $this->init($request,['type','id','user_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = $this->updateMemberState($p, 'active', 'pause');
	    if (count($msgs) == 0) {
	        $url = config('MYDOMAIN').'/opt/'.$p->type.'/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $this->redirectTo($url);
	    } else {
	        $this->view->errorMsg($msgs, $url, txt('BACK'), $p);
	    }
	}
	
	/**
	 * szüneteltetett tag újra aktiválja magát
	 * @param Request $request - type, id, user_id, csrToken
	 */
	public function activate(Request $request) {
	    $p = $this->init($request,['type','id','user_id']);
	    $this->checkCsrToken($request);
	    $this->createCsrToken($request, $p);
	    $msgs = $this->updateMemberState($p, 'pause', 'active');
	    if (count($msgs) == 0) {
	        $url = config('MYDOMAIN').'/opt/'.$p->type.'/form/id/'.$p->id.'/'.$p->csrToken.'/1';
	        $this->redirectTo($url);
	    } else {
	        $this->view->errorMsg($msgs, $url, txt('BACK'), $p);
	    }
	}
}
?>