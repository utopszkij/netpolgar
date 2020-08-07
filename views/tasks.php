<?php
/**
 * tasks kezelés viewer
 */
include_once './views/common.php';

/** user kezelés viewer osztály */
class TasksView  extends CommonView  {

     
    public function browser(Params $p) {
        $this->setTemplates($p,[]);
        $p->paginators = $this->makePaginators($p->total, $p->offset, $p->limit);
        $this->echoHtmlPage('taskslist',$p, 'tasks');
    }
    
    /**
     * echo tasks form
     * @param Params $p - csrToken, id, loggedState, loggedUser
     * @param Params $p
     */
    public function form(Params $p) {
        $this->setTemplates($p,['commentadd']);
        $this->echoHtmlPage('tasksform',$p, 'tasks');
    }
	
}
?>

