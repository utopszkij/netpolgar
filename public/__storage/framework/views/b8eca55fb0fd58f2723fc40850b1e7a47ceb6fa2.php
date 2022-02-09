<?php 
/**
 * create url from user record
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

?>
<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
      <!-- 
        params: members,parentType,parent,admin,users
            order,orderDir,filterStr,
            parentPath
      -->
      
<div id="membersBrowser" class="pageContainer row membersBrowser">
            	<div class="row filters">
            		<?php if(count($parentPath) > 0): ?>
            		    <div class="row parentPath">
				        	<ul>
				        	<?php $__currentLoopData = $parentPath; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				        		<li>
				        			<a href="<?php echo e(URL::to('/')); ?>/group/show/<?php echo e($p->id); ?>"
				        				title="<?php echo e(__('members.show')); ?>">
				        				<em class="fa fa-eye"></em>&nbsp;
				        				<?php echo e($p->name); ?>

				        			</a>
				        		</li>	
				        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				        	</ul>
				        </div>
				    <?php else: ?>
	        			<a href="<?php echo e(URL::to('/')); ?>/<?php echo e($parentType); ?>/show/<?php echo e($parent->id); ?>"
	        				title="<?php echo e(__('groups.show')); ?>">
	        				<em class="fa fa-eye"></em>&nbsp;
	        				<?php echo e($parent->name); ?>

	        			</a>
            		<?php endif; ?>
            	</div>
            	<h2><?php echo e(__('members.list')); ?></h2>
            	<div class="row searchForm">
            		<form method="get" id="memberSearch" action="">
            			<input type="text" id="filterStr" name="filterStr" 
            				value="<?php echo e($filterStr); ?>" />
            			<button class="btn btn-primary" type="submit"
            				title="<?php echo e(__('members.search')); ?>">
            				<em class="fa fa-search"></em>
            			</button>
            			<button class="btn btn-secondary" type="submit" 
            				onclick="$('#filterStr').val('');"
            				title="<?php echo e(__('members.clearSearch')); ?>">
            				<em class="fa fa-times"></em>
            			</button>
            			
            		</form>
            	</div>
				<table class="table table-bordered table-hover">
				    <thead>
				        <th class="name">
				        	<a href="?page=1&order=name">
				        	<?php echo e(__('members.name')); ?>

				        	<?php if($order == 'name'): ?>
				        		<?php if($orderDir == 'ASC'): ?>
				        			<em class="fa fa-caret-down"></em>
				        		<?php else: ?>
				        			<em class="fa fa-caret-up"></em>
				        		<?php endif; ?>
				        	<?php endif; ?>
				        	</a>
				        </th>
				        <th class="ranks">
				        	<?php echo e(__('members.ranks')); ?>

				        </th>
				    </thead>
				    <tbody>
				        <?php if($members->count() == 0): ?>
				        <tr>
				            <td colspan="2"><?php echo e(__('members.notrecords')); ?></td>
				        </tr>
				        <?php endif; ?>
				
				        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				        <tr>
				            <td>
				            	<a href="<?php echo e(url('/')); ?>/member/form/<?php echo e($parentType); ?>/<?php echo e($parentId); ?>/<?php echo e($member->name); ?>"
				            	   title="<?php echo e(__('members.show')); ?>">&nbsp;
			        				<img class="avatar" src="<?php echo e(avatar($member->profile_photo_path)); ?>" />&nbsp;
			        				<?php echo e($member->name); ?>

								</a>
				            </td>
				            <td>
				              <?php 
				              $w = explode(',',$member->ranks);
				              for ($i=0; $i<count($w); $i++) {
				                  $w[$i] = __('members.'.$w[$i]);
				              }
				              $member->ranks = implode(',',$w);
				              ?>
				              <?php echo e($member->ranks); ?>

				              <?php if($member->current_team_id === 0): ?>
				              	,<?php echo e(__('members.sysadmin')); ?>

				              <?php endif; ?>
				            </td>
				        </tr>
				        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				    </tbody>
				</table>
				<?php echo e($members->links()); ?>

				<?php if(\Auth::user()): ?>
    				<?php if(($admin->id > 0) | (\Auth::user()->current_team_id == 0)): ?>
    					<form class="form" method="post"
    					   action="<?php echo e(\URL::to('/')); ?>/membe/save">
    					   <?php echo csrf_field(); ?>
    					   <input type="hidden" name="parentType" value="<?php echo e($parentType); ?>" />
    					   <input type="hidden" name="parentId" value="<?php echo e($parentId); ?>" />
    					   <div class="form-group">
    					   	  <label><?php echo e(__('members.newUser')); ?></label>
    					   	  <select name="user_id" class="form-control">
    					   	    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    					   	    <option value="$user->id"><?php echo e($user->name); ?></option>
    					   	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    					   	  </select>
                				<button type="submit" class="btn btn-primary">
                					<em class="fa fa-check"></em>&nbsp;<?php echo e(__('Save')); ?>

                				</button>&nbsp;
    					   </div>
    					</form>
    				<?php endif; ?>
				<?php endif; ?>
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
<?php /**PATH /var/www/html/netpolgar/resources/views/members.blade.php ENDPATH**/ ?>