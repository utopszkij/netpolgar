<?php
include_once './views/common.php';
class LikeView  extends CommonView  {

	
	/**
	 * like részletek böngésző
	 * @param object $p (items, offset, limit, orderField, orderDir, filterStr
	 */
	public function list($p) {
	    $this->echoHtmlHead($p);
	    $backUrl = MYDOMAIN.'/opt/like/list';
	    ?>
        <body ng-app="app">
          <div ng-controller="ctrl" id="scope" style="display:none">
        	<div id="likeList">  
        		<form id="formLikeList" action="<?php echo MYDOMAIN; ?>/opt/like/list" 
        		    method="post" target="_self">
        		    <input type="hidden" name="{{csrToken}}" value="1" />
        		    <input type="hidden" name="offset" id="offset" value="{{offset}}" />
        		    <input type="hidden" name="limit" id="limit" value="{{limit}}" />
        		    <input type="hidden" name="orderField" id="orderField" value="{{orderField}}" />
        		    <input type="hidden" name="orderDir" id="orderDir" value="{{orderDir}}" />
        		    <input type="hidden" name="type" id="type" value="{{type}}" />
        		    <input type="hidden" name="id" id="id" value="{{id}}" />
        		    <input type="hidden" name="user_member" id="user_member" value="{{user_member}}" />
        		    <input type="hidden" name="like_type" id="like_type" value="{{like_type}}" />
        		    <h2>{{listTitle}}</h2>
            		<h3>{{subTitle}}</h3>
            		<table class="table table-striped" summary="likeDetails">
            			<thead class="thead-dark">
            				<tr>
            					<th id="thLike_type">&nbsp;<em class="fa"></em></th>
            					<th id="thNick"><em class="fa"></em></th>
            					<th id="thUser_Member">&nbsp;<em class="fa"></em></th>
            				</tr>
            			<thead>
            			<tbody>
            				<tr ng-repeat="item in items" scope="row"  class="{{item.trClass}}">
            					<td class="tdLike_type">{{txt('LIKE_TYPE_'+item.like_type)}}</td>
            					<td class="tdNick">{{item.nick}}</td>
            					<td class="tdUser_member">{{txt('LIKE_MEMBER_'+item.user_member)}}</td>
            				</tr>
           				</tbody>
            		</table>
            		<?php $this->echoPaginator($p->total, $p->offset, $p->limit); ?>
        		</form>
			</div>    
		    <div class="clear"></div>
	      </div><!-- #scope -->
	      <script type="text/javascript">
			/**
			* paginátor click rutin - offset beállítása, form submit
			* @param int offset
			*/
			function paginatorClick(offset) {
				  $('#offset').val(offset);
				  $('#formUsersList').submit();
				  console.log('paginatorClick '+offset);
				  return false;
			}
	      </script>
		  <?php $this->loadJavaScriptAngular('like',$p); ?>
        </body>
        </html>
	    <?php
	}
	
}
?>

