<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>  
	<div id="teamContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2><?php echo e(__('team.teams')); ?></h2>
            </div>
        </div>
    </div>
    <div class="row path" style="margin-top: 5px;">
    <?php $pathSeparator = ''; ?>
    <?php $__currentLoopData = $info->path; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    	<var class="pathItem">
			<a href="<?php echo e(route('teams.show',["team" => $item->id])); ?>">
				&nbsp;<?php echo $pathSeparator; ?>&nbsp;<?php echo e($item->name); ?> 			
			</a>    	
    	</var>
	    <?php $pathSeparator = '<em class="fas fa-caret-right"></em>'; ?>
	 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	    
	 </div>    

    <?php if(($info->status == 'active') & 
         (count($info->userRank) > 0) &
         (!$info->parentClosed)): ?>
    <div class="row buttons">
       <a class="btn btn-primary" 
         href="<?php echo e(route('parents.teams.create',["parent" => $parent])); ?>">
        	<em class="fas fa-plus"></em>
        	<?php echo e(__('team.add')); ?>

       </a>
    </div>
    <?php endif; ?>
     
    <table class="table table-bordered">
        <tr>
            <th><?php echo e(__('team.status')); ?></th>
            <th><?php echo e(__('team.name')); ?></th>
            <th><?php echo e(__('team.description')); ?></th>
        </tr>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if ($value->avatar == '') $value->avatar = URL::to('/').'/img/team.png'; ?>
        <tr>
            <td><?php echo e(__('team.'.$value->status)); ?></td>
            <td>
            	<a href="<?php echo e(route('teams.show', $value->id)); ?>">
            	<img src="<?php echo e($value->avatar); ?>" class="logo" alt="logo" title="logo" />
            	<?php echo e($value->name); ?>

            	</a>
            </td>
            <td><?php echo e(\Str::limit($value->description, 100)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php if(count($data) > 0): ?>
    <div class="row help">
		Részletekért és további lehetőségért kattints a csoport nevére!				
    </div>
    <?php else: ?>
    <div><?php echo e(__('team.notrecord')); ?></div>
    <?php endif; ?>  
    <?php echo $data->links(); ?>

    
  </div>        
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>  
<?php /**PATH /var/www/html/netpolgar/resources/views/team/index.blade.php ENDPATH**/ ?>