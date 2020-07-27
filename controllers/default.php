<?php
/**
 * kezdő lap megjelenítése kontroller
 */
include_once './controllers/common.php';

/** kontroller osztály */
class DefaultController extends CommonController {
    
    /**
     * kezdőlap megjelenítés task
     * @param Request $request
     */
	public function default(Request $request) {
      // echo frontpage
	    // $request->set('sessionid','0');
	    $request->set('lng','hu');
	    $request->set('option','frontpage');
	    $p = $this->init($request,[]); 
	    $p->cookieEnabled = $request->sessionGet('cookieEnabled',false);
	    $projectsModel = $this->getModel('projects');
	    $groupsModel = $this->getModel('groups');
	    $usersModel = $this->getModel('users');
	    $p->newProjects = $projectsModel->newProjects(3);
	    for ($i=0; $i<count($p->newProjects); $i++) {
	        if ($p->newProjects[$i]->avatar == '') {
	            $p->newProjects[$i]->avatar = './images/noimage.png';
	        }
	        if ($p->newProjects[$i]->description == '') {
	            $p->newProjects[$i]->description = 'A projekt létrehozója nem adott meg szöveges információkat';
	        }
	    }
	    $p->newGroups = $groupsModel->newGroups(3);
	    for ($i=0; $i<count($p->newGroups); $i++) {
	        if ($p->newGroups[$i]->avatar == '') {
	            $p->newGroups[$i]->avatar = './images/noimage.png';
	        }
	        if ($p->newGroups[$i]->description == '') {
	            $p->newGropus[$i]->description = 'A csoport létrehozója nem adott meg szöveges információkat';
	        }
	    }
	    $p->newUsers = $usersModel->newUsers(3);
	    for ($i=0; $i<count($p->newUsers); $i++) {
	        if ($p->newUsers[$i]->avatar == '') {
	            $p->newUsers[$i]->avatar = './images/noavatar.png';
	        }
	        if ($p->newUsers[$i]->pubinfo == '') {
	            $p->newUsers[$i]->pubinfo = 'A felhasználó nem adott meg szöveges információkat';
	        }
	    }
	    $this->view->display($p);
	}
}
?>