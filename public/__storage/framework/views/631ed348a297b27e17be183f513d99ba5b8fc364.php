<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>  

	<?php if($errors->any()): ?>
		<?php
			// hiba üzenettel validátor hiba miatt lett aktiválva
			// ilyenkor a korábban kitöltött értékeket kell felhozni.
			$team->name = \Request::old('name');
			$team->description = \Request::old('description');
			$team->avatar = \Request::old('avatar');
			$team->config->ranks = explode(',',\Request::old('ranks'));
			$team->config->close = \Request::old('close');
			$team->config->memberActivate = \Request::old('memberActivate');
			$team->config->memberExclude = \Request::old('memberExclude');
			$team->config->rankActivate = \Request::old('rankActivate');
			$team->config->rankClose = \Request::old('rankClose');
			$team->config->projectActivate = \Request::old('projectActivete');
			$team->config->productActivate = \Request::old('productActivate');
			$team->config->subTeamActivate = \Request::old('subTeamActivate');
			$team->config->debateActivate = \Request::old('debateActivate');

		?>
	<?php endif; ?>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 <?php if($team->id > 0): ?>
                <h2><?php echo e(__('team.edit')); ?></h2>
                <?php else: ?>
                <h2><?php echo e(__('team.add')); ?></h2>
                <?php endif; ?>
            </div>
        </div>
    </div>
 
    <div class="row path" style="margin-top: 5px;">
    <?php $pathSeparator = ''; ?>
    <?php $__currentLoopData = $info->path; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    	<?php if($item->id != $team->id): ?>
    	<var class="pathItem">
			<a href="<?php echo e(route('teams.show',["team" => $item->id])); ?>">
				&nbsp;<?php echo $pathSeparator; ?>&nbsp;<?php echo e($item->name); ?> 			
			</a>    	
    	</var>
    	<?php endif; ?>
	   <?php $pathSeparator = '<em class="fas fa-caret-right"></em>'; ?>
	 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	    
	 </div>    
 
 	<?php if($team->id > 0): ?>
    <form action="<?php echo e(route('teams.update',$team->id)); ?>" method="POST">
   <?php else: ?>
    <form action="<?php echo e(route('parents.teams.store', $team->parent)); ?>" method="POST">
   <?php endif; ?> 
   <?php echo csrf_field(); ?>
 	<?php if($team->id > 0): ?>
     <?php echo method_field('PUT'); ?>
	<?php endif; ?>   
         <input type="hidden" name="id" value="<?php echo e($team->id); ?>" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="<?php echo e($team->parent); ?>" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label><?php echo e(__('team.status')); ?>:</label>
                    <?php echo e(__('team.'.$team->status)); ?>

                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label><?php echo e(__('team.name')); ?>:</label>
                    <input type="text" name="name" value="<?php echo e($team->name); ?>" class="form-control" placeholder="Név">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label><?php echo e(__('team.avatar')); ?>:</label>
                    <input type="text" name="avatar" value="<?php echo e($team->avatar); ?>" class="form-control" placeholder="URL">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label style="vertical-align: top;">
                    <?php echo e(__('team.description')); ?>

                    </label>
                    <textarea class="form-control" cols="80" rows="5" style="height:150px" 
                    name="description" placeholder="Leírás"><?php echo $team->description; ?></textarea>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label style="vertical-align: top;">Beállítások:</label>
						<div class="config" style="display:inline-block; width:500px">
						  <br />Tisztségek:  <input type="text" 
						  style="width:400px" 
						  name="ranks" value="<?php echo e(implode(',',$team->config->ranks)); ?>" />
						  <br />
						  <input type="number" min="1" max="100" 
						  name="close" value="<?php echo e($team->config->close); ?>" />
						  % támogatottság kell a csoport lezárásához,<br />
						  <input type="number" min="1" max="100" 
						  name="memberActivate" value="<?php echo e($team->config->memberActivate); ?>" />
						  fő támogató kell tag felvételéhez,<br />
						  <input type="number" min="1" max="100" 
						  name="memberExclude" value="<?php echo e($team->config->memberExclude); ?>" />
						  % támogatottság kell tag kizárásához,<br />
						  <input type="number" min="1" max="100" 
						  name="rankActivate" value="<?php echo e($team->config->rankActivate); ?>" />	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  <input type="number" min="1" max="100" 
						  name="rankClose" value="<?php echo e($team->config->rankClose); ?>" />
						  % támogatottság kell tisztség visszavonásához,<br />
						  <input type="number" min="1" max="100" 
						  name="projectActivate" value="<?php echo e($team->config->projectActivate); ?>" />
						  fő támogató kell projekt aktiválásához,<br />
						  <input type="number" min="1" max="100" 
						  name="productActivate" value="<?php echo e($team->config->productActivate); ?>" />
						  % támogatottság kell termék közzé tételéhez,<br />
						  <input type="number" min="1" max="100" 
						  name="subTeamActivate" value="<?php echo e($team->config->subTeamActivate); ?>" />
						  fő támogató kell alcsoport aktiválásához,<br />
						  <input type="number" min="1" max="100" 
						  name="debateActivate" value="<?php echo e($team->config->debateActivate); ?>" />
						  fő támogató kell eldöntendő vita inditásához
						</div>
                </div>
               </div> 
            </div>
	         <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
	              <button type="submit" class="btn btn-primary">
	              		<em class="fas fa-check"></em><?php echo e(__('team.save')); ?>

	              </button>
	              <a class="btn btn-secondary" href="<?php echo e(route('parents.teams.index',["parent" => $team->parent])); ?>">
	                  <em class="fas fa-ban"></em>
	                  <?php echo e(__('team.cancel')); ?>

	              </a>
	            </div>
	         </div>  
        </div>
   
    </form>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>  
<?php /**PATH /var/www/html/netpolgar/resources/views/team/form.blade.php ENDPATH**/ ?>