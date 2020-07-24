<?php
include_once './views/common.php';
class GroupsView  extends CommonView  {
    
    /**
     * group adatképernyő
     * @param object $p - $p->item - group record mezői, $p->formTitle, $p->user, 
     *    $p->userid, $p->filterUser
     */
    public function form(Params $p) {
        $this->setTemplates($p,['groupssubmenu','likebar']);
        $this->echoHtmlPage('groupsform',$p, 'groups');
    }
	
	/**
	 * group törlés megerősítő kérdés
	 * @param object $p -  $p->item = GroupRecord
	 */
	public function deleteGroup(Params $p) {
	    $this->setTemplates($p,[]);
	    $this->echoHtmlPage('groupsdelete',$p, 'groups');
	}
	
	public function browser(Params $p) {
	    $this->setTemplates($p,[]);
	    $this->echoHtmlPage('groupslist',$p, 'groups');
	}
	
}
?>

