<?php
/**
 * create url from user photo_path
 * @param unknown $user
 * @return string
 */
function avatar($profile_photo_path) {
    if ($profile_photo_path != '') {
        $result = URL::to('/').'/storage/app/public/'.$profile_photo_path;
    } else {
        $result = URL::to('/').'/img/noavatar.png';
    }
    return $result;
}

/**
 * member rekord status modositása a likeCount/dislikeCount alapján
 * @param unknown $member
 * 1. $member->parent_type és ->parent_id alapján parent rekord olvasása
 * 2. parent memberCount olvasása
 * 3. $member->status, $parent->config, $memberCount és $likeCount, $dsiLikeCouunt
 *    alapján szükség szerint státus modositás (az adatbázisban is)
 */
function statusAdjust(& $member, int $likeCount, $disLikeVount) {
    
}


/**
 * echo select option
 * @param unknown $act
 * @param unknown $value
 */
function option(string $act, string $value) {
    if ($act == $value) {
        echo '<option value="'.$value.'" selected="selected">'.__('members.'.$value).'</option>'."\n";
    } else {
        echo '<option value="'.$value.'">'.__('members.'.$value).'</option>'."\n";
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
        params: members,  parent, parentType, admin
      -->
<div id="memberForm" class="pageContainer memberForm">
	<h4><?php echo e($parent->name); ?></h4>
	<p><?php echo e(__('members.'.$parentType )); ?> <?php echo e(__('members.member')); ?></p>
	<p><img src="<?php echo e(avatar($members[0]->profile_photo_path)); ?>" class="avatar" />&nbsp;
    	<?php echo e($members[0]->name); ?>&nbsp;
    </p>
	<?php if(count($members) > 0): ?>
	    <form id="formMember" method="post" 
    	action="<?php echo e(URL::to('/')); ?>/member/save" class="form col-lg-12">
    	<?php echo csrf_field(); ?>
    	<input type="hidden" name="parentType" value="<?php echo e($parentType); ?>" />
    	<input type="hidden" name="parentId" value="<?php echo e($parent->id); ?>" />
    	<input type="hidden" name="name" value="<?php echo e($members[0]->name); ?>" />
    	<?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    		  <?php 
    	      // like,dislike információk
              $messageModel = new \App\Models\Messages();
              $likeCount = $messageModel->where('parent_type','=',$parentType.'member')
              ->where('parent_id','=',$member->id)
              ->where('type','=','like')
              ->count();
              $messageModel = new \App\Models\Messages();
              $disLikeCount = $messageModel->where('parent_type','=',$parentType.'member')
              ->where('parent_id','=',$member->id)
              ->where('type','=','dislike')
              ->count();
              $user = \Auth::user();
              if ($user) {
                  $messageModel = new \App\Models\Messages();
                  $userLiked = ($messageModel->where('parent_type','=',$parentType.'member')
                      ->where('parent_id','=',$member->id)
                      ->where('type','=','like')
                      ->where('user_id','=',$user->id)
                      ->count() > 0);
                  $messageModel = new \App\Models\Messages();
                  $userDisLiked = ($messageModel->where('parent_type','=',$parentType.'member')
                      ->where('parent_id','=',$member->id)
                      ->where('type','=','dislike')
                      ->where('user_id','=',$user->id)
                      ->count() > 0);
              } else {
                  $userLiked = 0;
                  $userDisLiked = 0;
                  $admin = false;
              }
              statusAdjust($member, $likeCount, $disLikeCount);
              ?>
    	
    		<div class="form-group">
    			<?php if(\Auth::user()): ?>
    		   		<?php if(($admin) | (\Auth::user()->current_team_id == 0)): ?>
        				<select name="status_<?php echo e($member->id); ?>" class="form-control">
        				<?php echo e(option($member->status,'proposal')); ?>

        				<?php echo e(option($member->status,'active')); ?>

        				<?php echo e(option($member->status,'closed')); ?>

        				<?php echo e(option($member->status,'excluded')); ?>

        				<?php echo e(option($member->status,'deleted')); ?>

        				</select>&nbsp;
        			<?php else: ?>
        				<?php echo e(__('members.'.$member->status)); ?>&nbsp;
        			<?php endif; ?>
        		<?php else: ?>
       				<?php echo e(__('members.'.$member->status)); ?>&nbsp;
    			<?php endif; ?>
    			<?php echo e(__('members.'.$member->rank)); ?>&nbsp;
    			<?php if($member->status == 'proposal'): ?>
    			    &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo e(__('members.like')); ?>&nbsp;
    			    <?php if($userLiked): ?>
    			    	<em class="fa fa-check"></em> 
    			    <?php endif; ?>
    				<a href="<?php echo e(URL::to('/')); ?>/like/<?php echo e($parentType); ?>member/<?php echo e($member->id); ?>/like">
    					<em class="fa fa-thumbs-up"></em>&nbsp;
    				</a>
    				(<?php echo e($likeCount); ?>)
    			<?php endif; ?>
    			<?php if($member->status == 'active'): ?>
    			    &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo e(__('members.dislike')); ?>&nbsp;
    			    <?php if($userDisLiked): ?>
    			    	<em class="fa fa-check"></em> 
    			    <?php endif; ?>
    				<a href="<?php echo e(URL::to('/')); ?>/like/<?php echo e($parentType); ?>member/<?php echo e($member->id); ?>/dislike">
    					<em class="fa fa-thumbs-down"></em>&nbsp;
    				</a>
    				(<?php echo e($disLikeCount); ?>)
    			<?php endif; ?>
    		</div>
    	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    	<?php if(\Auth::user()): ?>
    		<?php if(($admin) | (\Auth::user()->current_team_id == 0)): ?>
    			<div class="form-group add">
    			    <br />
        			<?php echo e(__('members.add')); ?>

        			<select name="rank" class="form-control">
        				<option value="">&nbsp;</option>
        				<option value="member"><?php echo e(__('members.member')); ?></option>
        				<option value="admin"><?php echo e(__('members.admin')); ?></option>
        			</select>
        		</div>
    			<div class="form-buttons">
    				<button type="submit" class="btn btn-primary">
    					<em class="fa fa-check"></em>&nbsp;<?php echo e(__('Save')); ?>

    				</button>&nbsp;
            		<a href="<?php echo e(URL::previous()); ?>" class="btn btn-secondary">
            			<em class="fa fa-reply"></em>&nbsp;<?php echo e(__('Cancel')); ?>

            		</a>
    			</div> 
    		<?php endif; ?>
    	<?php endif; ?>
    	</form>
	<?php endif; ?>	
</div>  
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/member_form.blade.php ENDPATH**/ ?>