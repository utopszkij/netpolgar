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

?>
<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
      <!-- 
        params: message,  parent, sender
      -->
<div id="moderatorForm" class="pageContainer row moderatorForm">
    <h2><?php echo e(__('messages.moderatorForm')); ?></h2>
    <form id="formModerator" method="post" action="<?php echo e(URL::to('/')); ?>/message/savemoderation" class="form">
    	<?php echo csrf_field(); ?>
    	<input type="hidden" name="id" value="<?php echo e($message->id); ?>" />
    	<div class="form-group">
			<label><?php echo e(__('messages.target')); ?>:</label>
			<var><?php echo e($parent->name); ?></var> <?php echo e(__('messages.'.$message->parent_type)); ?>   	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('messages.sender')); ?>:</label>
			<var><?php echo e($sender->name); ?></var>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('messages.created_at')); ?>:</label>
			<var><?php echo e($message->created_at); ?></var>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('messages.value')); ?>:</label>
			<textarea cols="80" rows="10" name="value" class="form-control"><?php echo e($message->value); ?></textarea>    	
    	</div>
    	<div class="form-group">
			<label><?php echo e(__('messages.moderatorinfo')); ?>:</label>
			<textarea cols="80" rows="10" name="moderatorinfo" class="form-control"><?php echo e($message->moderatorinfo); ?></textarea>    	
    	</div>
    	<div class="buttons">
    		<button type="submit" class="btn btn-primary">
    			<em class="fa fa-check"></em>&nbsp;<?php echo e(__('messages.save')); ?>

    		</button>&nbsp;
    		<a href="<?php echo e(URL::previous()); ?>" class="btn btn-secondary">
    			<em class="fa fa-reply"></em>&nbsp;<?php echo e(__('messages.cancel')); ?>

    		</a>
    	</div>
    </form>
</div>  
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/moderator.blade.php ENDPATH**/ ?>