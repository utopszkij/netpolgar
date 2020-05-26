<?php
use PhpParser\Node\Expr\BinaryOp\Identical;

include_once './controllers/common.php';
class LikeController extends CommonController {
    
    /**
     * like/dislike képernyő rész kirajzolása (nem task hanem be inkludolt rutin)
     * @param string $type objektum tipus ('group',...)
     * @param int $id objektum Id
     * @param string $label szöveg a képernyőre
     * @param string $help szöveg a képernyőre
     */
    public function show(string $type, int $id, string $label, string $help) {
        global $REQUEST;
        $p = $this->init($REQUEST,[]);
        ?>
        <div id="divLike">
        	<label><?php echo txt($label); ?></label>
       		<a id="likeUp" class="fa fa-thumbs-up" href="#" title="<?php echo txt('YES'); ?>">&nbsp;</a>
       		<a id="likeUpTotal" href="#">&nbsp;</a>/<a id="likeUpMember" href="#">&nbsp;</a>
       		&nbsp;
       		<a id="likeDown" class="fa fa-thumbs-down" href="#" title="<?php echo txt('NO'); ?>">&nbsp;</a>
       		<a id="likeDownTotal" href="#">&nbsp;</a>/<a id="likeDownMember" href="#">&nbsp;</a>
       		&nbsp;
       		<span id="likeHelp">(<?php echo txt($help); ?>)</span>
        </div>
        <div id="likeDetails" style="display:none;">
        	<p style="text-align:right"><span href="#" onclick="$('#likeDetails').hide();" class="btn btn-secondary">x</span></p>
        	<iframe width="100%" height="700" title="ifrmLike"></iframe>
        </div>
        
        <script type="text/javascript">
	       $(function() {
          		var data = {"type":"<?php echo $type; ?>", "id":"<?php echo $id; ?>"};
          		global.working(true);
           		global.post('./opt/like/likeshow', data, function(res) {
           			$('#likeUpTotal').html(res.upTotal);
           			$('#likeDownTotal').html(res.downTotal);
           			$('#likeUpMember').html(res.upMember);
           			$('#likeDownMember').html(res.downMember);
              		global.working(false);
           		});
				$('#likeUp').click(function() {
	           		var data = {"type":"<?php echo $type; ?>", "id":"<?php echo $id; ?>"};
	          		global.working(true);
	           		$.post('./opt/like/likeupclick', data, function(res) {
	           			$('#likeUpTotal').html(res.upTotal);
	           			$('#likeDownTotal').html(res.downTotal);
	           			$('#likeUpMember').html(res.upMember);
	           			$('#likeDownMember').html(res.downMember);
	              		global.working(false);
	           		});
	           		return false;
				});
				$('#likeDown').click(function() {
	           		var data = {"type":"<?php echo $type; ?>", "id":"<?php echo $id; ?>"};
	          		global.working(true);
	           		$.post('./opt/like/likedownclick', data, function(res) {
	           			$('#likeUpTotal').html(res.upTotal);
	           			$('#likeDownTotal').html(res.downTotal);
	           			$('#likeUpMember').html(res.upMember);
	           			$('#likeDownMember').html(res.downMember);
	              		global.working(false);
	           		});
	           		return false;
				});
       			$('#likeUpTotal').click(function() {
				    $('#likeDetails').show();
				    $('#likeDetails iframe').attr('src','./opt/like/list'+
				       '/type/<?php echo $type; ?>'+
				       '/user_member/all/like_type/like'+
				        '/id/<?php echo $id; ?>');
				    window.scrollTo(0,0);    
	           		return false;
				});
       			$('#likeDownTotal').click(function() {
				    $('#likeDetails').show();
				    $('#likeDetails iframe').attr('src','./opt/like/list'+
				       '/type/<?php echo $type; ?>'+
				       '/user_member/all/like_type/dislike'+
				        '/id/<?php echo $id; ?>');
				    window.scrollTo(0,0);    
	           		return false;
				});
       			$('#likeUpMember').click(function() {
				    $('#likeDetails').show();
				    $('#likeDetails iframe').attr('src','./opt/like/list'+
				       '/type/<?php echo $type; ?>'+
				       '/user_member/1/like_type/like'+
				        '/id/<?php echo $id; ?>');
				    window.scrollTo(0,0);    
	           		return false;
				});
       			$('#likeDownMember').click(function() {
				    $('#likeDetails').show();
				    $('#likeDetails iframe').attr('src','./opt/like/list'+
				       '/type/<?php echo $type; ?>'+
				       '/user_member/1/like_type/dislike'+
				        '/id/<?php echo $id; ?>');
				    window.scrollTo(0,0);    
	           		return false;
				});
           });
        </script>
        <?php 
	}
		
	/**
	 * like részletek böngésző csak admin használhatja
	 * @param Request $request {type, id}
	 * -sessionba jöhet: offset, limit
	 */
	public function list(Request $request) {
	    $p = $this->init($request,[]);
	    $this->model = $this->getModel('list');
	    $this->view = $this->getView('list');
	    $p->type = $request->input('type');
	    $p->id = $request->input('id');
	    $p->user_member = $request->input('user_member');
	    $p->like_type = $request->input('like_type');
	    $p->offset = $request->input('offset', $request->sessionGet('likeOffset',0));
	    $p->limit = $request->input('limit', $request->sessionGet('likeLimit',20));
	    $p->oderField = $request->input('orderfield', $request->sessionGet('likeOrderField',''));
	    $p->orderDir = $request->input('orderdir', $request->sessionGet('likeOrderDir',''));
	    $p->filterStr = $request->input('filterstr', $request->sessionGet('likeFilterStr',''));
        $request->sessionSet('likeOffset',$p->offset);
        $request->sessionSet('likeLimit',$p->limit);
        $p->total = 0;
        $p->items = $this->model->getRecords($p, $p->total);
        $p->listTitle = '';
        $p->subTitle = '';
        if ($p->user_member != 'all') {
            $p->subTitle .= txt('LIKE_MEMBER_1').' ';
        }
        $p->subTitle .= txt('LIKE_TYPE_'.$p->like_type); 
        
        // váltakozó trClass beállítás /bootstrap table-stiped nem müködik :( /
        $trClass = 'tr0';
        foreach ($p->items as $item) {
            $item->trClass = $trClass;
            if ($trClass == 'tr0') {
                $trClass = 'tr1';
            } else {
                $trClass = 'tr0';
            }
        }
        $this->createCsrToken($request, $p);
        $this->view->list($p);
	}
	
	/**
	 * AJAX backend  like adatok kiolvasása az adatbázisból
	 * @param Request - type, id,  
	 *   sessionban user
	 * echo json string {upTotal, downTotal, upMember, downMember}    
	 */
	public function likeshow(Request $request) {
	    if (!headers_sent()) {
	        header("Content-type: application/json; charset=utf-8");
	    }
	    $this->init($request,'like');
	    $this->model = $this->getModel('list');
	    $this->view = $this->getView('list');
	    $type = $request->input('type');
	    $id = $request->input('id');
	    echo JSON_encode($this->model->getCounts($type, $id));
	}
	
	/**
	 * like click kezelés segédrutinja
	 * @param Request $request
	 * @param string $name1 'like'|'unlike' erre kattintott
	 * @param string $name2 'like'|'unlike' nem erre kattintott
	 */
	protected function likeChange(Request $request, string $name1, string $name2) {
	    $p = $this->init($request,[]);
	    $this->model = $this->getModel('list');
	    $this->view = $this->getView('list');
	    $type = $request->input('type');
	    $id = $request->input('id');
	    $user = $p->user;
	    if ($user->id > 0) {
	        if ($this->model->check($type,$id,$user->id) == $name1) {
	            $this->model->remove($type, $id, $name1, $user->id);
	        } else {
	            $this->model->set($type, $id, $name1, $user->id);
	            $this->model->remove($type, $id, $name2, $user->id);
	        }
	        
	        
	    }
	    $this->likeshow($request);
	}
	
	/**
	 * AJAX backend  - up ikonra kattintás
	 * @param Request $request - type, Identical
	 *    sessionban bejelentkezett user, userMember
	 */
	public function likeupclick(Request $request) {
	    $this->likeChange($request, 'like','dislike');
	}
	/**
	 * AJAX backend  - down ikonra kattintás
	 * @param Request $request - type, Identical
	 *    sessionban bejelentkezett user, userMember
	 */
	public function likedownclick(Request $request) {
	    $this->likeChange($request, 'dislike','like');
	}
		
}
?>