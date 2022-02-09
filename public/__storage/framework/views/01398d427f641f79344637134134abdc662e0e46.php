<?php 
// params:  group,  parent, parentPath, user, creator, member, parentMember

/* Megoldamdó
*   jogosultság ellenörzés:
*      group like  parent-group-member | sysadmin
*      goup dislike group-member
*   csoportba jelentkezés, kilépés   
*/


/**
 * create url from user record
 * @parm User $user
 * @return string
 */
function avatar($user) {
    if ($user->profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$user->profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

if ($parent) {
    $parent_id = $parent->id;
    $parent_name = $parent->name;
} else {
    $parent_id = 0;
    $parent_name = '';
}

/**
 * echo select option 
 * @param unknown $act
 * @param unknown $value
 */
function option(string $act, string $value) {
    if ($act == $value) {
        echo '<option value="'.$value.'" selected="selected">'.__('groups.'.$value).'</option>'."\n";
    } else {
        echo '<option value="'.$value.'">'.__('groups.'.$value).'</option>'."\n";
    }
}
?>

<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<div id="groupShow" class="pageContainer row groupShow">
    <div class="row filters">
    	<?php if(count($parentPath) > 0): ?>
    	    <div class="row parentPath">
    	    	<h4><?php echo e(__('groups.parents')); ?></h4>
            	<ul>
            	<?php $__currentLoopData = $parentPath; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            		<li>
            			<a href="<?php echo e(URL::to('/')); ?>/group/show/<?php echo e($p->id); ?>"
            				title="<?php echo e(__('groups.show')); ?>">
            				<em class="fa fa-eye"></em>
            			</a>
            			&nbsp;
            			<a href="<?php echo e(URL::to('/')); ?>/groups/<?php echo e($p->id); ?>/0/0"
            				title="<?php echo e(__('groups.open')); ?>">
            				<em class="fa fa-folder-open"></em>
            				<?php echo e($p->name); ?>

            			</a>
            		</li>	
            	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            	</ul>
            </div>
    	<?php endif; ?>
    </div>
    <div class="row">
	<h2 class="title">
   			<?php echo e(__('groups.details')); ?>

   	</h2>
  	<a href="#" onclick="$('#submenu').toggle()" class="submenuIcon" title="<?php echo e(_('submenu')); ?>">
   			<em class="fa fa-bars"></em>
   	</a>
   	<div class="submenu col-lg-3" id="submenu">
   		<ul>
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/members/group/<?php echo e($group->id); ?>">
   					<em class="fa fa-user"></em>&nbsp;<?php echo e(__('groups.members')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/groups/<?php echo e($group->id); ?>/0/0">
   					<em class="fa fa-play"></em>&nbsp;<?php echo e(__('groups.subgroups')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/projects/<?php echo e($group->id); ?>/0">
   					<em class="fa fa-cogs"></em>&nbsp;<?php echo e(__('groups.projects')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/products/<?php echo e($group->id); ?>">
   					<em class="fa fa-cart-plus">&nbsp;</em><?php echo e(__('groups.products')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/messages/group/<?php echo e($group->id); ?>">
   					<em class="fa fa-comment">&nbsp;</em><?php echo e(__('groups.messages')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/votes/group/<?php echo e($group->id); ?>">
   					<em class="fa fa-check-square">&nbsp;</em><?php echo e(__('groups.votes')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/events/group/<?php echo e($group->id); ?>">
   					<em class="fa fa-calendar">&nbsp;</em><?php echo e(__('groups.events')); ?>

   				</a>
   			</li>	
   			<li>
   				<a href="<?php echo e(URL::to('/')); ?>/fields/group/<?php echo e($group->id); ?>">
   					<em class="fa fa-folder-open">&nbsp;</em><?php echo e(__('groups.files')); ?>

   				</a>
   			</li>	
   			<?php if($member): ?>
       			<?php if($member->rank == 'admin'): ?>
       			<li>
       				<a href="<?php echo e(URL::to('/')); ?>/group/form/<?php echo e($group->id); ?>/<?php echo e($group->parent_id); ?>">
       					<em class="fa fa-edit">&nbsp;</em><?php echo e(__('groups.edit')); ?>

       				</a>
       			</li>	
       			<?php endif; ?>
   			<?php endif; ?>
   		</ul>
   	</div>
    <form id="formShow" method="post" 
    	action="<?php echo e(URL::to('/')); ?>/group/save" class="form col-lg-9">
    	<?php echo csrf_field(); ?>
    	<input type="hidden" name="id" value="<?php echo e($group->id); ?>" />
    	<input type="hidden" name="parent_id" value="<?php echo e($group->parent_id); ?>" />
    	<input type="hidden" name="created_at" value="<?php echo e($group->created_at); ?>" />
    	<input type="hidden" name="created_by" value="<?php echo e($group->created_by); ?>" />
    	<input type="hidden" name="updated_at" value="<?php echo e($group->updated_at); ?>" />
    	<div class="form-group">
			<label><?php echo e(__('groups.name')); ?>:</label>
			<input type="text" name="name" disabled="disabled" class="form-control" value="<?php echo e($group->name); ?>" />    	
    	</div>
    	<?php if(auth()->guard()->check()): ?>
    	<div class="form-group">
			<label></label>
			<?php if($member): ?>
    		<a class="btn btn-primary" href="<?php echo e(URL::to('/')); ?>/member/signin/group/<?php echo e($group->id); ?>">
    			<em class="fa fa-sign-out"></em>&nbsp;<?php echo e(__('groups.signout')); ?>

    		</a>
			<?php else: ?>
    		<a class="btn btn-primary" href="<?php echo e(URL::to('/')); ?>/member/signin/group/<?php echo e($group->id); ?>">
    			<em class="fa fa-sign-in"></em>&nbsp;<?php echo e(__('groups.signin')); ?>

    		</a>
    		<?php endif; ?>
		</div>
		<?php endif; ?>    	
    	<div class="form-group">
			<label><?php echo e(__('groups.description')); ?>:</label>
			<textarea cols="80" rows="5" readonly="readonly" name="description" class="form-control"><?php echo e($group->description); ?></textarea>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.avatar')); ?>:</label>
			<input type="text" size="80" id="avatar" name="avatar" disabled="disabled" 
				class="form-control" value="<?php echo e($group->avatar); ?>" onchange="avatarChange()" />
			<img id="imgAvatar" src="<?php echo e($group->avatar); ?>" class="avatar" />    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.status')); ?>:</label>
			<?php if($group->status == 'proposal'): ?>
				<var><?php echo e(__('groups.'.$group->status)); ?></var>
				<input type="hidden" name="status" disabled="disabled" value="<?php echo e($group->status); ?>" />
			<?php else: ?>
				<select name="status" disabled="disabled">
					<?php 
					option($group->status,'proposal');
					option($group->status,'active');
					option($group->status,'closed');
					option($group->status,'paused');
					option($group->status,'deleted');
					?>
				</select> 
			<?php endif; ?>
		</div>  
    	<div class="form-group">
			<label><?php echo e(__('groups.config')); ?>:</label>
			<textarea cols="80" rows="10" name="config" readonly="readonly" class="form-control"><?php echo e($group->config); ?></textarea>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.creator')); ?>:</label>
			<var><?php echo e($creator->name); ?></var>    	
    	</div>
    	
    	<div class="buttons">
    		<a href="<?php echo e(URL::previous()); ?>" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;<?php echo e(__('groups.ok')); ?>

    		</a>
    	</div>
    	<div class="likes">
    	<?php if($group->status == 'proposal'): ?>
    		<?php if($userLiked): ?>
    			<em class="fa fa-check"></em>&nbsp;
    		<?php endif; ?>
    		<?php if(auth()->guard()->check()): ?>
        		<?php if(($parentMember) | (\Auth::user()->current_team_id == 0)): ?>
            		<a href="<?php echo e(URL::to('/')); ?>/like/group/<?php echo e($group->id); ?>/like">
        	    		<?php echo e(__('groups.like')); ?>&nbsp;
            			<em class="fa fa-thumbs-up"></em>
            		</a>
        		<?php else: ?> 
    	    		<?php echo e(__('groups.like')); ?>&nbsp;
        			<em class="fa fa-thumbs-up"></em>
        		<?php endif; ?>
    		<?php else: ?> 
    	    		<?php echo e(__('groups.like')); ?>&nbsp;
        			<em class="fa fa-thumbs-up"></em>
    		<?php endif; ?>
    		&nbsp;
    		<var><a href="<?php echo e(URL::to('/')); ?>/likelist/group/<?php echo e($group->id); ?>/like"><?php echo e($likeCount); ?></a></var>
    	<?php endif; ?>
    	<?php if($group->status == 'active'): ?>
    		<?php if($userDisLiked): ?>
    			<em class="fa fa-check"></em>&nbsp;
    		<?php endif; ?>
    		<?php if($member): ?>
        		<a href="<?php echo e(URL::to('/')); ?>/like/group/<?php echo e($group->id); ?>/dislike">
    	    		<?php echo e(__('groups.dislike')); ?>&nbsp;
        			<em class="fa fa-thumbs-down"></em>
        		</a>
    		<?php else: ?> 
        		<?php echo e(__('groups.dislike')); ?>&nbsp;
       			<em class="fa fa-thumbs-down"></em>
    		<?php endif; ?>
    		&nbsp;
    		<var><a href="<?php echo e(URL::to('/')); ?>/likelist/group/<?php echo e($group->id); ?>/dislike"><?php echo e($disLikeCount); ?></a></var>
    	<?php endif; ?>
    		
    	</div>
    </form>
</div>  
<script type="text/javascript">
function avatarChange() {
	$('#imgAvatar').attr('src',$('#avatar').val());
}
</script>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/group_show.blade.php ENDPATH**/ ?>