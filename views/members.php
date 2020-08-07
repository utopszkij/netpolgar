<?php
/**
 * user kezelés viewer
 */
include_once './views/common.php';

/** user kezelés viewer osztály */
class MembersView  extends CommonView  {

     
    public function browser(Params $p) {
        $this->setTemplates($p,[]);
        $p->paginators = $this->makePaginators($p->total, $p->offset, $p->limit);
        $this->echoHtmlPage('memberslist',$p, 'members');
    }
    
    /**
     * echo members form
     * @param Params $p - csrToken, type, obbjectid, id, user, userState, loggedState, loggedUser
     * @param Params $p
     */
    public function form(Params $p) {
        $this->setTemplates($p,['id','commentadd']);
        $this->echoHtmlPage('membersform',$p, 'members');
    }
	
}
?>

