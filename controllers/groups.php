<?php
include_once 'controllers/common.php';
class GroupsController extends CommonController {
    protected $userGroupRights = false;
    
    protected function isUserAdmin(UserRecord $user, bool $userAdmin, int $groupId): bool {
        if (!$this->userGroupRights) {
            $this->userGroupRights = $this->model->getUserGroupRights($user);
        }
        if ((in_array($groupId, $this->userGroupRights)) | ($userAdmin)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
        
	/**
	 * groups böngésző
	 * @param Request $request {userAdmin, user, avatarUrl, opcionálisan: userid, parentid}
	 * -sessionba jöhet: groupsOffset, groupsOrderField, groupsOrderDir, groupsFilterStr, 
	 *                   groupsLimit, groupsUserid 
	 * a "tree" orderField jelentése: sa struktura szerinti sorrend                  
	 */
	public function list(Request $request, array $msgs = []) {
	    $p = $this->init($request,[]);
	    $p->userId = $request->input('userid', $request->sessionGet('groupsUserId',0));
	    $p->parentId = $request->input('parentid', $request->sessionGet('groupsParentId',0));
	    if ($p->parentId < 0) {
	        $p->parentId = 0;
	    }
	    $p->msgs = $msgs;
	    if ($p->userAdmin) {
	        $p->userId = 0;
	    }
	    if ($p->userId > 0) {
	        $userModel = $this->getModel('users');
	        $p->filterUser = $userModel->getById($p->userId);
	    }
	    $p->offset = $request->input('offset', $request->sessionGet('gropsOffset',0));
	    $p->limit = $request->input('limit', $request->sessionGet('groupsLimit',20));
	    $p->filterStr = $request->input('filterStr', $request->sessionGet('groupsFilterStr',''));
	    $p->orderField = $request->input('orderField', $request->sessionGet('groupsOrderField','tree'));
	    $p->orderDir = $request->input('orderDir', $request->sessionGet('groupsOrderDir','ASC'));
	    $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $p->parentId);
	    $request->sessionSet('groupsOffset',$p->offset);
	    $request->sessionSet('groupsLimit',$p->limit);
	    $request->sessionSet('grouspOrderField',$p->orderField);
	    $request->sessionSet('groupsOrderDir',$p->orderDir);
	    $request->sessionSet('groupsFilterStr',$p->filterStr);
	    $request->sessionSet('groupsUserId',$p->userId);
	    $request->sessionSet('groupsParentId',$p->parentId);
	    $p->total = 0;
	    $p->items = $this->model->getRecords($p, $p->total);
	    $p->parents = $this->model->getGroupPath($p->parentId);
	    if ($p->parentId == 0) {
	        $p->formTitle = 'GROUPS_LIST';
	        $p->formSubTitle = '';
	    } else {
	        $parentGroup = $this->model->getRecord($p->parentId);
	        $p->formTitle = $parentGroup->name;
	        $p->formSubTitle = 'SUB_GROUPS_LIST';
	    }
	    
	    $this->createCsrToken($request, $p);
	    $this->view->browser($p);
      } // list task
      
      /**
       * group adatképernyő
       * @param Request $request {userAdmin, user, avatarUrl, groupid}
       * -sessionba jöhet: groupsOffset, groupsOrderField, groupsOrderDir, groupsFilterStr,
       *                   groupsLimit, groupsUserid
       */
      public function groupform(Request $request) {
          $p = $this->init($request,[]);
          $this->createCsrToken($request, $p);
          $backUrl = MYDOMAIN.'/opt/groups/list';
          $p->groupId = $request->input('groupid',0);
          $p->groupUserId = $request->sessionGet('groupsUserId',0);
          $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $p->groupId);
          
          $membersModel = $this->getModel('members');
          $p->userMember = $membersModel->isMember('group', $p->groupId, $p->loggedUser->id);
          
          $p->formTitle = 'GROUP';
          $p->msgs = [];
          if ($p->groupId > 0) {
              $p->parents = $this->model->getGroupPath($p->groupId);
              $p->item = $this->model->getRecord($p->groupId);
              $p->id = $p->groupId;
              if ($p->item->id > 0) {
                  $this->view->form($p);
              } else {
                  $this->view->errorMsg(['NOT_FOUND'],$backUrl,txt('OK'),$p);
              }
          } else {
              $p->parents = [];
              $p->item = new GroupRecord();
              $p->item->name = txt('GROUPS_ROOT');
              $p->item->reg_mode = 'admin';
              $p->id = 0;
              $this->view->form($p);
          }
      }
      
      /**
       * új group felvitele
       * @param Request $request {userAdmin, user, avatarUrl, parentgroupid}
       * -sessionba jöhet: groupsOffset, groupsOrderField, groupsOrderDir, groupsFilterStr,
       *                   groupsLimit, groupsUserid
       */
      public function add(Request $request) {
          $p = $this->init($request, []);
          $this->checkCsrToken($request);
          $backUrl = MYDOMAIN.'/opt/groups/list';
          $this->createCsrToken($request, $p);
          $p->parentId = $request->input('parentid',0);
          $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $p->parentId);
          $p->parents = $this->model->getGroupPath($p->parentId);
          if ($p->userAdmin) {
              $p->item = new GroupRecord();
              $p->item->id = 0;
              $p->item->name = '';
              $p->item->description = 'A javasolt alcsoportok automatikusan aktiválásra kerülnek ha'.
' a támogatók száma 10 -el meghaladja az ellenzők számát.'. 
' Az aktív csoportok automatikusan lezáródnak ha a tagok 80% -a negatívan értékeli a csoportot.'. 
' A tagsági javaslatok, jelentkezések automatikusan aktiválódnak ha a csoport tagok támogatása'.
' 2 -vel meghaladja a csoport tagok ellenzését.'.
' A csoport tag automatikusan kizárásra kerül ha a csoport tagok 90% -a negatívan értékeli.'.
' A csoport adminisztrátorok módosíthatják a csoport adatait, státuszát, alcsoportokat tagokat'.
' vehetnek fel, kezelhetnek';               ;
              $p->item->reg_mode = 'self';
              $p->item->state = 'active';
              $p->item->parent = $p->parentId;
              $p->item->group_to_active = 10;
              $p->item->group_to_close = 80;
              $p->item->member_to_active = 2;
              $p->item->member_to_exclude = 90;
              $p->formTitle = 'ADD_SUB_GROUP';
              $p->groupId = 0;
              $p->id = 0;
              $this->view->form($p);
          } else {
              $this->view->errorMsg(['ACCESS_VIOLATION'],$backUrl, txt('OK'), $p);
          }
      }
      
      /**
       * group törlése biitonsági kérdés
       * @param Request $request {userAdmin, user, avatarUrl, groupid}
       * -sessionba jöhet: groupsOffset, groupsOrderField, groupsOrderDir, groupsFilterStr,
       *                   groupsLimit, groupsUserid
       */
      public function remove(Request $request) {
          $p = $this->init($request, []);
          $this->checkCsrToken($request);
          $p->item = $this->model->getRecord($request->input('groupid',0));
          $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $p->item->id);
          $p->backUrl = MYDOMAIN.'/opt/groups/list';
          if (($p->userGroupAdmin) & ($p->item->id > 0)) {
              $this->createCsrToken($request, $p);
              $msgs = $this->model->canDelete($p->item, $p->userGroupAdmin);
              if (count($msgs) > 0) {
                  $this->view->errorMsg($msgs, $p->backUrl, txt('OK'), $p);
              } else  if ($p->item->id > 0) {
                  $p->formTitle = 'GROUP';
                  $this->view->deleteGroup($p);
              } else {
                  $this->view->errorMsg(['NOT_FOUND'], $p->backUrl, txt('OK'), $p);
              }
          } else {
              $this->view->errorMsg(['ACCESS_VIOLATION'], $p->backUrl, txt('OK'), $p);
          }
      }
      
      /**
       * group képernyő feldolgozása - ellenörzés, tárolás
       * @param Request $request // {userAdmin, user, avatarUrl, groupid, form mezők}
       */
      public function save(Request $request) { 
        $p = $this->init($request, []);
        $this->checkCsrToken($request);
        $backUrl = MYDOMAIN.'/opt/groups/list';
        $item = new GroupRecord();
        foreach ($item as $fn => $fv) {
            $item->$fn = $request->input($fn, $fv);
        }
        $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $item->id);
        $p->userParentGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $item->parent);
        $this->createCsrToken($request, $p);
        $jo = false;
        if (($item->id == 0) & ($p->userParentGroupAdmin)) {
            $jo = true;
        }
        if (($item->id > 0) & ($p->userGroupAdmin)) {
            $jo = true;
        }
        if (!$jo) {
            $this->view->errorMsg(['NOT_FOUND'], $backUrl, txt('OK'), $p);
        }
        $msgs = $this->model->check($item);
        if (count($msgs) == 0) {
            $msgs = $this->model->save($item, $p->loggedUser);
        } 
        if (count($msgs) == 0) {
            $request->set('parentid', $item->parent);
            $this->list($request, ['GROUP_SAVED']);
            // $this->view->successMsg(['GROUP_SAVED'], $backUrl, txt('OK'), $p);
        } else {
            $p->item = $item;
            if ($p->item->id > 0) {
                $p->formTitle = 'GROUP';
            } else {
                $p->formTile = 'NEW_GROUP';
            }
            $p->msgs = $msgs;;
            $p->parents = $this->model->getGroupPath($item->id);
            $p->groupId = $item->id;
            $p->id = $item->id;
            $this->view->form($p);
        }
      }
      
      /**
       * group törlés végrehajtása
       * @param Request $request {userAdmin, user, avatarUrl, groupId}
       * -sessionba jöhet: groupsOffset, groupsOrderField, groupsOrderDir, groupsFilterStr,
       *                   groupsLimit, groupsUserid
       */
      public function doremovegroup(Request $request) {
          $p = $this->init($request, []);
          $this->checkCsrToken($request);
          $p->item = $this->model->getRecord($request->input('groupId',0));
          $p->userGroupAdmin = $this->isUserAdmin($p->loggedUser, $p->userAdmin, $p->item->id);
          $backUrl = MYDOMAIN.'/opt/groups/list';
          $this->createCsrToken($request, $p);
          if (($p->userGroupAdmin) & ($p->item->id > 0)) {
              $msgs = $this->model->canDelete($p->item, $p->userGroupAdmin);
              if (count($msgs) > 0) {
                  $this->view->errorMsg(['NOT_FOUND'], $backUrl, txt('OK'), $p);
              } else  if ($p->item->id > 0) {
                  $msgs = $this->model->delete($p->item);
                  if (count($msgs) == 0) {
                      $this->view->successMsg(['GROUP_DELETED'], $backUrl, txt('OK'), $p);
                  } else {
                      $this->view->errorMsg($msgs, $backUrl, txt('OK'), $p);
                  }
              } else {
                  $this->view->errorMsg(['NOT_FOUND'], $backUrl, txt('OK'), $p);
              }
          } else {
              $this->view->errorMsg(['ACCESS_VIOLATION'], $backUrl, txt('OK'), $p);
          }
      }
      
      /**
       * AJAX backend    alrekordok beolvasása
       * @param Request $request   - parentId
       * @return json str 
       *     {"parentId":"###", items:[{"id":###,"name":"xxx", "avatar":"xxx", "childs":bools}, ....]}
       */
      public function loadsubgroup(Request $request) {
          if (!headers_sent()) {
            header("Content-type: application/json; charset=utf-8");
          }
          $this->init($request, []);
          $parentId = $request->input('parentId',-1);
          echo $this->model->getSubGroup($parentId);
      }
      
}
?>