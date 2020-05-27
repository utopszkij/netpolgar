<?php
include_once 'controllers/common.php';
class MembersController extends CommonController {
	

	/**
	 * members böngésző 
	 * ha userGroupAdmin akkor van "add" és "invite" gomb is, 
	 * @param Request $request type, objectid
	 * -sessionba jöhet: user, offset, orderField, orderDir, filterStr, limit
	 */
	public function list(Request $request) {
	    $p = $this->init($request,['type','objectid','groups']);
	    $this->createCsrToken($request, $p);
	    $p->type = $request->input('type','group');
	    $p->objectId = $request->input('objectid','0');
	    $p->typeId = $p->type.$p->objectId;
	    if ($p->type == 'group') {
	        $groupModel = $this->getModel('groups');
	        $p->group = $groupModel->getRecord($p->objectId);
	        $p->backUrl = MYDOMAIN.'/opt/groups/groupform/groupid/'.$p->objectId.'/'.$p->csrToken.'/1';
	        $p->userGroupAdmin = $this->model->isUserAdmin($p->type, $p->objectId, $p->loggedUser->id);
	        $p->formTitle = txt('GROUP_MEMBERS');
	    }
	    $p->offset = $request->input('offset', $request->sessionGet($p->typeId.'MembersOffset',0));
	    $p->limit = $request->input('limit', $request->sessionGet($p->typeId.'MembersLimit',20));
	    $p->filterStr = $request->input('filterStr', $request->sessionGet($p->typeId.'MembersFilterStr',''));
	    $p->orderField = $request->input('orderField', $request->sessionGet($p->typeId.'MembersOrderField','nick'));
	    $p->orderDir = $request->input('orderDir', $request->sessionGet($p->typeId.'MembersOrderDir','ASC'));
	    $request->sessionSet($p->typeId.'MembersOffset',$p->offset);
	    $request->sessionSet($p->typeId.'MembersLimit',$p->limit);
	    $request->sessionSet($p->typeId.'MembersOrderField',$p->orderField);
	    $request->sessionSet($p->typeId.'MembersOrderDir',$p->orderDir);
	    $request->sessionSet($p->typeId.'MembersFilterStr',$p->filterStr);
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
     * új tag felvitel, meghívó küldés csak userGroupAdmin használhatja 
     * ez egy user böngésző, rejtett mezőben a type, typeid, state
     * a névre kattintás hatása: rejtett memberid beállítása és "save"
     * @param Request $request csrtoken, type, objectid, limit, offset, 
     * filterStr, state='active'|'invited'
     * sessionban: csrToken, user
     */
	public function add(Request $request, string $state = 'invite') {
	    echo 'nincs kész';
	}
	
    /**
     * memberForm tárolása (felvitel vagy update) csak userGroupAdmin használhatja
     * ha invite statust visz fel akkor emailt is küld
     * @param Request $request - csrtoken, type, objectid, memberid
     * session: user, csrToken
     */
	public function save(Request $request) {
	    echo 'nincs kész';
	}
	
	/**
	 * member rekord törlés végrehajtása csak userGroupAdmin használhatja
	 * @param Request $request - type, objectid, rekord mezői
	 * session: user
	 */
	public function remove(Request $request) {
	    echo 'nincs kész';
	}
}
?>