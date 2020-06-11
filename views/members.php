<?php
/**
 * user kezelés viewer
 */
include_once './core/browser.php';

/** user kezelés viewer osztály */
class MembersView  extends BrowserView  {

    /**
     * tábla fejléc kirajzolása
     * @param array $items
     * @param string $order
     * @param $order_dir
     * @return void
     */
    protected function echoTableHead(array $items, string $order, string $order_dir) {
        if (count($items) > 0) {
            ?>
		  	 <thead class="thead-dark">
		  	 <tr>
		  	 <?php
		  	 foreach ($items[0] as $fn => $fv) {
		  	 	if ($fn == $order) {
		  	 		$thClass = 'order';
		  	 		if (($order_dir == 'DESC') | ($order_dir == 'desc')) {
		  	 			$thIcon = '<em class="fa fa-caret-up"></em>';
		  	 		} else {
		  	 			$thIcon = '<em class="fa fa-caret-down"></em>';
		  	 		}
		  	 	} else {
		  	 		$thClass = 'unorder';
		  	 		$thIcon = '';
		  	 	}
		  	 	if ($fn == 'avatar') {
		  	 	    $fn = '';
		  	 	}
		  	 	if ($fn != 'id') {
		  	 	?>
		  	 	<th class="<?php echo 'th_'.$fn.' '.$thClass; ?>" style="cursor:pointer" 
		  	 	    onclick="titleClick('<?php echo $fn; ?>','<?php echo $order; ?>','<?php echo $order_dir; ?>')">
		  	 	    <?php echo txt($fn).'&nbsp;'.$thIcon; ?>
		  	 	</th>
		  	 	<?php
		  	 	}
		  	 }
		  	 echo "</tr>\n</thead>\n";
	  	 }
	  }	     
     
     /**
     * egy tábla sor kirajzolása
     * @param object $item - legyen benne id
     * @param string $trClass css class name a "tr" elemhez
     * @return void
     */
     protected function echoTableRow($item, $trClass) {
     		echo '<tr onclick="itemClick('."'$item->id'".')" class="'.$trClass.'" style="cursor:pointer">'."\n";
     		foreach ($item as $fn => $fv) {
     		    if ($fn != 'id') {
     		        if ($fn == 'state') {
     		            $fv = txt($fv);
     		        }
     		        if ($fn == 'avatar') {
     		            $fv = '<img src="'.$fv.'" style="height:30px"/>';
     		        }
				    echo '<td class="td_'.$fn.'">'.$fv.'</td>'."\n";
     		    }
     		}
     		echo "</tr>\n";
     }
    
     protected function echoSearch(Params $p) {
         ?>
         <div class="search">
             <input type="text" name="searchstr" id="searchstr" value="<?php echo $p->searchstr; ?>" />
             <select name="filterstate">
             	<option value=""<?php if ($p->filterState == '') echo ' selected="selected"'; ?>>
             		<?php echo txt('ALLSTATE'); ?>
             	</option>
             	<option value="aspirant"<?php if ($p->filterState == 'aspirant') echo ' selected="selected"'; ?>>
             		<?php echo txt('aspirant'); ?>
             	</option>
             	<option value="active"<?php if ($p->filterState == 'active') echo ' selected="selected"'; ?>>
             		<?php echo txt('active'); ?>
             	</option>
             	<option value="excluded"<?php if ($p->filterState == 'excluded') echo ' selected="selected"'; ?>>
             		<?php echo txt('excluded'); ?>
             	</option>
             	<option value="exited"<?php if ($p->filterState == 'exited') echo ' selected="selected"'; ?>>
             		<?php echo txt('exited'); ?>
             	</option>
             	<option value="admin"<?php if ($p->filterState == 'admin') echo ' selected="selected"'; ?>>
             		<?php echo txt('admin'); ?>
             	</option>
             </select>
             <div style="display:inline-block; width:auto">
                 <button type="button" id="searchBtn" onclick="searchClick()" class="btn btn-primary">
                 <em class="fa fa-search"></em>
                 </button>
                 <button type="button" id="delSearchBtn" onclick="delSearchClick()" class="btn btn-danger">
                 <em class="fa fa-times"></em>
                 </button>
             </div>
         </div>
         <?php
     }
     
    public function browser(Params $p) {
        echo '<div id="membersList">'."\n";
        $this->browserForm($p);
        echo '</div>'."\n";
    }
    
    /**
     * echo members form
     * @param Params $p - csrToken, type, obbjectid, id, user, userState, loggedState, loggedUser
     * @param Params $p
     */
    public function form(Params $p) {
        $this->echoHtmlHead($p);
        ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
			<?php $this->echoHtmlPopup(); ?>
            <?php $this->echoNavbar($p); ?>
          	<div id="memberForm">
          	    <h2><img src="{{group.avatar}}" />&nbsp;{{group.name}}</h2>
                <h2>{{txt('MEMBER_FORM')}}</h2>
        			<div class="alert alert-danger" ng-if="msgs.length > 0">
        				<p ng-repeat="msg in msgs">{{txt(msg)}}</p>
        			</div>
                <div id="memberForm2">
                	<img src="{{user.avatar}}" />
                	<h3>{{user.nick}}</h3>
                	<p>{{txt(userState)}}</p>
                	<p class="buttons">
                		<a class="btn btn-secondary"  target="_self"
                		    href="{{LNG.MYDOMAIN}}/opt/members/setadmin/type/{{type}}/objectid/{{objectId}}/userid/{{user.id}}"
                		    ng-if="(userState == 'active') && (loggedState == 'admin') && (user.id != loggedUser.id)">
                			<em class="fa fa-chess-king"></em>
                			{{txt('SET_MANAGER')}}
                		</a>	
                		<a class="btn btn-secondary"  target="_self"
                		    href="{{LNG.MYDOMAIN}}/opt/members/setnotadmin/type/{{type}}/objectid/{{objectId}}/userid/{{user.id}}"
                		    ng-if="(userState == 'admin') && (loggedState == 'admin') && (user.id != loggedUser.id)">
                			<em class="fa fa-chess-pawn"></em>
                			{{txt('SET_NO_MANAGER')}}
                		</a>
                		&nbsp;
                		<a class="btn btn-secondary" target="_self"
                		    href="{{LNG.MYDOMAIN}}/opt/members/list/type/{{type}}/objectid/{{objectId}}">
                			<em class="fa fa-reply"></em>{{txt('BACK')}}
                		</a>
                		
                	</p>
					<?php  
					include_once './controllers/like.php';
					$likeController = new LikeController();
					$likeController->show('groupMember', $p->objectId.'.'.$p->user->id,'MEMBER_LIKE','MEMBER_LIKE_HELP');
					include_once './controllers/comment.php';
					$commentController = new CommentController();
					$commentController->show('groupMember', $p->objectId.'.'.$p->user->id,'COMMENTS');
				    ?>                	
              	</div><!-- #memberForm2 -->  
			 	<div class="clear"></div>
			</div><!--  #memberForm --> 	
		 	<?php $this->echoFooter(); ?>
	      </div><!-- #scope -->
		  <?php $this->loadJavaScriptAngular('members',$p); ?>
        </body>
        </html>
        <?php 		
    }
	
}
?>

