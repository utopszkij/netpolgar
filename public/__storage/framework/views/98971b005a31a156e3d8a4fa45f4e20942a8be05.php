<?php 
/**
 * create url from user record
 * @param unknown $user
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

function option($act, $value) {
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
      <!-- 
        params: group,  parent, parentPath, user, creator
      -->
<div id="groupForm" class="pageContainer row groupForm">
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
    <?php if($group->id == 0): ?> 
    	<h2><?php echo e(__('groups.add')); ?></h2>
    <?php else: ?>
    	<h2><?php echo e(__('groups.edit')); ?></h2>
    <?php endif; ?>	
    <form id="formGroup" method="post" action="<?php echo e(URL::to('/')); ?>/group/save" class="form">
    	<?php echo csrf_field(); ?>
    	<input type="hidden" name="id" value="<?php echo e($group->id); ?>" />
    	<input type="hidden" name="parent_id" value="<?php echo e($group->parent_id); ?>" />
    	<input type="hidden" name="created_at" value="<?php echo e($group->created_at); ?>" />
    	<input type="hidden" name="created_by" value="<?php echo e($group->created_by); ?>" />
    	<input type="hidden" name="updated_at" value="<?php echo e($group->updated_at); ?>" />
    	<input type="hidden" name="activated_at" value="<?php echo e($group->activated_at); ?>" />
    	<input type="hidden" name="closed_at" value="<?php echo e($group->closed_at); ?>" />
    	<div class="form-group">
			<label><?php echo e(__('groups.name')); ?>:</label>
			<input type="text" name="name" class="form-control" value="<?php echo e($group->name); ?>" />    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.description')); ?>:</label>
			<textarea cols="80" rows="5" name="description" class="form-control"><?php echo e($group->description); ?></textarea>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.avatar')); ?>:</label>
			<input type="text" size="80" id="avatar" name="avatar" 
				class="form-control" value="<?php echo e($group->avatar); ?>" onchange="avatarChange()" />
			<img id="imgAvatar" src="<?php echo e($group->avatar); ?>" class="avatar" />    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.status')); ?>:</label>
			<?php if($group->status == 'proposal'): ?>
				<var><?php echo e(__('groups.'.$group->status)); ?></var>
				<input type="hidden" name="status" value="<?php echo e($group->status); ?>" />
			<?php else: ?>
				<select name="status">
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
			<textarea cols="80" rows="10" name="config" class="form-control"><?php echo e($group->config); ?></textarea>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('groups.creator')); ?>:</label>
			<var><?php echo e($creator->name); ?></var>    	
    	</div>
    	
		  	
    	
    	<div class="form-buttons">
    		<button type="submit" class="btn btn-primary">
    			<em class="fa fa-check"></em>&nbsp;<?php echo e(__('groups.save')); ?>

    		</button>&nbsp;
    		<a href="<?php echo e(URL::previous()); ?>" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;<?php echo e(__('groups.cancel')); ?>

    		</a>
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
<?php /**PATH /var/www/html/netpolgar/resources/views/group_form.blade.php ENDPATH**/ ?>