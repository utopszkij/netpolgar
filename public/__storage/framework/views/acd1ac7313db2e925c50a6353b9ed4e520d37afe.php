<?php 
/**
 * create url from user record
 * @param unknown $user
 * @return string
 */
function avatar() {
    $user = \Auth::user();
    if ($user->profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$user->profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

if ($member) {
    $member_id = $member->id;
} else {
    $member_id = 0;
}
if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<script src="https://meet.jit.si/external_api.js"></script>
<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
      <!-- 
        params: items, parentType, parent, parentId 
                parentPath
                member, total, offset, filterStr
      -->
<div id="messagesBrowser" class="pageContainer row messagesBrowser">
		<h2><?php echo e($parent->name); ?></h2>
		<p> <?php echo e(__('messages.'.$parentType)); ?></p>
    	<h3><?php echo e(__('messages.list')); ?></h3>
    	<div class="row searchForm">
    		<form method="get" id="messagesSearch" action="">
    			<input type="text" id="filterStr" name="filterStr" 
    				value="<?php echo e($filterStr); ?>" />
    			<button class="btn btn-primary" type="submit"
    				title="<?php echo e(__('messages.search')); ?>">
    				<em class="fa fa-search"></em>
    			</button>
    			<button class="btn btn-secondary" type="submit" 
    				onclick="$('#filterStr').val('');"
    				title="<?php echo e(__('messages.clearSearch')); ?>">
    				<em class="fa fa-times"></em>
    			</button>
    			
    		</form>
    	</div>
    	<div>
    	<?php echo e(__('messages.online')); ?>: <var id="onlineCount" style="cursor:pointer">0</var>
    	</div>
    	<div id="messagesList" style="display:inline-block; width:100%; float:left">
				    <div class="messages">
				        <!--  @ i f ($items->count() == 0) -->
				        <?php if(count($items) == 0): ?>
				            <?php echo e(__('messages.notrecords')); ?>

				        <?php endif; ?>
				
				        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				        	<div class="messageItem">
				        	    <span class="sender">
				        	    	<img src="<?php echo e(URL::to('/')); ?>/storage/app/public/<?php echo e($item->profile_photo_path); ?>" class="avatar" />
				        	    	&nbsp;<?php echo e($item->name); ?>

				        	    </span>
				        		<strong>&nbsp;#<?php echo e($item->id); ?></strong> 
				        		<span class="sendTime">&nbsp;<?php echo e($item->created_at); ?></span>
				        		<?php if($member): ?>
				        			<?php if(($member->rank == 'admin') | (\Auth::user()->current_tea_id == 0)): ?>
				        	    	<br />
				        	    	<pre><?php echo $item->value; ?></pre>
			        	    		<a href="<?php echo e(URL::to('/')); ?>/message/moderator/<?php echo e($item->id); ?>" title="<?php echo e(__('messages.moderation')); ?>">
			        	    			<em class="fa fa-edit"></em>
			        	    		</a>
				        	    	<?php else: ?>
					        	    	<br /><pre><?php echo $item->value; ?></pre>
				        			<?php endif; ?> 
				        		<?php else: ?>
				        	    	<br /><pre><?php echo $item->value; ?></pre>
				        	    <?php endif; ?>
				        	</div>
				        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				    </div>
				<!--  { { $ items->links() } } -->
		</div>
		<div id="onlineMembers" style="display:none; width:15%"; float:right">
			<h3><?php echo e(__('messages.online')); ?></h3>
			<div id="onlineList">
			</div>
		</div>		
		<div style="clear:both"></div>
		<?php if($member): ?>
		<div class="help">
		    <?php echo __('messages.help'); ?>

			
		</div>
		<div style="display:none">
			<iframe id="frmHidde"></iframe>
		</div>
		<div id="jitsi">
		<script type="text/javascript">
            var domain = "meet.jit.si";
            var w = window.innerWidth - 15;
            var options = {
                roomName: "<?php echo e($parentType); ?>_<?php echo e($parent->name); ?>",
                width: w,
                height: w,
                parentNode: undefined,
                configOverwrite: {},
                interfaceConfigOverwrite: {
                    filmStripOnly: true
                },
                userInfo: {
        				email: '<?php echo e(\Auth::user()->email); ?>',
        				displayName: '<?php echo e(\Auth::user()->name); ?>'
    			}
            }
            var avatar = "<?php echo e(avatar()); ?>";
            var api = new JitsiMeetExternalAPI(domain, options);
			var s = api.getParticipantsInfo();
			$('#onlineCount').html(s.length);
			api.addListener('outgoingMessage', function(p) {
				if (p.message != '') {
					$('#frmHidde').attr('src',"<?php echo e(URL::to('/')); ?>/messageadd/<?php echo e($parentType); ?>/<?php echo e($parent->id); ?>/"
					+encodeURI(p.message));
				}	
			});
			api.addListener('videoConferenceJoined', function(p) {
					api.executeCommand('displayName', '<?php echo e(\Auth::user()->name); ?>');
					api.executeCommand('avatarUrl', avatar);
					var s = api.getParticipantsInfo();
					$('#onlineCount').html(s.length);
			});
			
			$('#onlineCount').click(function() {
			    var i = 0;
				var s = api.getParticipantsInfo();
				var div = $('#onlineList');
				$('#onlineCount').html(s.length);
				div.html('');
				for (i=0; i < s.length; i++) {
					div.append('ä href="<?php echo e(URL::to('/')); ?>/user/show/'+s[i].displayName+'">'+
						'<img src="'+s[i].avatarURL+'" class="avatar" />'+
						s[i].displayName+'</a><br />');
				}
 			    $('#messagesList').css('width','70%');
			    $('#onlineMembers').show();
			});
        </script>
		
		</div>
		<?php endif; ?>
</div>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/messages.blade.php ENDPATH**/ ?>