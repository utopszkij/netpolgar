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

if ($member) {
    $member_id = $member->id;
} else {
    $member_id = 0;
}
if ($admin) {
    $admin_id = $admin->id;
} else {
    $admin_id = 0;
}
if ($parent) {
    $parent_id = $parent->id;
} else {
    $parent_id = 0;
}
?>
<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
      <!-- 
        params: groups, member, admin, order, orderDir, filerStr, 
                parent, parentPath
                member és admin lehet false is!
                parentPath array of group_id
      -->
<div id="groupsBrowser" class="pageContainer row groupsBrowser">
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
				        			<a href="<?php echo e(URL::to('/')); ?>/groups/<?php echo e($p->id); ?>/<?php echo e($member_id); ?>/<?php echo e($admin_id); ?>"
				        				title="<?php echo e(__('groups.open')); ?>">
				        				<em class="fa fa-folder-open"></em>
				        				<?php echo e($p->name); ?>

				        			</a>
				        		</li>	
				        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				        	</ul>
				        </div>
            		<?php endif; ?>
            		<?php if($member): ?>
            		<div class="row"><label><?php echo e(__('groups.isMember')); ?></label>
            			<a href="<?php echo e(URL::to('/')); ?>/member/show/<?php echo e($member->id); ?>"> 
            				<img src="<?php echo e(avatar($member)); ?>" class="avatar" />
            				<?php echo e($member->name); ?>

            			</a>	
            		</div>
            		<?php endif; ?>
            		<?php if($admin): ?>
            		<div class="row"><label><?php echo e(__('groups.isAdmin')); ?></label> 
            			<a href="<?php echo e(URL::to('/')); ?>/member/show/<?php echo e($admin->id); ?>"> 
		            		<img src="<?php echo e(avatar($admin)); ?>" class="avatar" />
        		    		<?php echo e($admin->name); ?>

        		    	</a>	
            		</div>
            		<?php endif; ?>
            	</div>
            	<h2><?php echo e(__('groups.list')); ?></h2>
            	<div class="row searchForm">
            		<form method="get" id="groupSearch" action="">
            			<input type="text" id="filterStr" name="filterStr" 
            				value="<?php echo e($filterStr); ?>" />
            			<button class="btn btn-primary" type="submit"
            				title="<?php echo e(__('groups.search')); ?>">
            				<em class="fa fa-search"></em>
            			</button>
            			<button class="btn btn-secondary" type="submit" 
            				onclick="$('#filterStr').val('');"
            				title="<?php echo e(__('groups.clearSearch')); ?>">
            				<em class="fa fa-times"></em>
            			</button>
            			
            		</form>
            	</div>
				<table class="table table-bordered table-hover">
				    <thead>
				        <th class="id">
				        	<a href="?page=1&order=groups.id">
				        	<?php echo e(__('groups.id')); ?>

				        	<?php if($order == 'groups.id'): ?>
				        		<?php if($orderDir == 'ASC'): ?>
				        			<em class="fa fa-caret-down"></em>
				        		<?php else: ?>
				        			<em class="fa fa-caret-up"></em>
				        		<?php endif; ?>
				        	<?php endif; ?>
				        	</a>
				        </th>
				        <th class="name">
				        	<a href="?page=1&order=groups.name">
				        	<?php echo e(__('groups.name')); ?>

				        	<?php if($order == 'groups.name'): ?>
				        		<?php if($orderDir == 'ASC'): ?>
				        			<em class="fa fa-caret-down"></em>
				        		<?php else: ?>
				        			<em class="fa fa-caret-up"></em>
				        		<?php endif; ?>
				        	<?php endif; ?>
				        	</a>
				        </th>
				        <th class="status">
				        	<a href="?page=1&orde=groups.status">
				        	<?php echo e(__('groups.status')); ?>

				        	<?php if($order == 'groups.status'): ?>
				        		<?php if($orderDir == 'ASC'): ?>
				        			<em class="fa fa-caret-down"></em>
				        		<?php else: ?>
				        			<em class="fa fa-caret-up"></em>
				        		<?php endif; ?>
				        	<?php endif; ?>
				        	</a>
				        </th>
				    </thead>
				    <tbody>
				        <?php if($groups->count() == 0): ?>
				        <tr>
				            <td colspan="4"><?php echo e(__('groups.notrecords')); ?></td>
				        </tr>
				        <?php endif; ?>
				
				        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				        <tr>
				        	<td><?php echo e($group->id); ?></td>
				            <td>
				            	<a href="<?php echo e(url('/')); ?>/groups/<?php echo e($group->id); ?>/0/0"
				            	   title="<?php echo e(__('groups.open')); ?>">
			        				<em class="fa fa-folder-open"></em>
								</a>
								&nbsp;			            	
				            	<a href="<?php echo e(url('/')); ?>/group/show/<?php echo e($group->id); ?>"
				            		title="<?php echo e(__('groups.show')); ?>">
		        					<em class="fa fa-eye"></em>
									&nbsp;
				            		<img src="<?php echo e($group->avatar); ?>" class="avatar" />
				            		<?php echo e($group->name); ?>

				            	</a>
				            </td>
				            <td><?php echo e($group->status); ?></td>
				        </tr>
				        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				    </tbody>
				</table>
				<?php echo e($groups->links()); ?>

				<div class="buttons">
					<?php if(auth()->guard()->check()): ?>
					<a href="<?php echo e(URL::to('/')); ?>/group/form/0/<?php echo e($parent_id); ?>" class="btn btn-primary">
						<em class="fa fa-plus"></em>
						<?php echo e(__('groups.add')); ?>

					</a>
					<?php endif; ?>
				</div>
            </div>
        </div>
    </div>
</div>  
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/groups.blade.php ENDPATH**/ ?>