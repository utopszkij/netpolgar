<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>  
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
            	 <?php if($team->id > 0): ?>
                <h2>Edit Product</h2>
                <?php else: ?>
                <h2>Add new Product</h2>
                <?php endif; ?>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="<?php echo e(route('parents.teams.index',["parent" => $team->parent])); ?>"> Back</a>
            </div>
        </div>
    </div>
 
	<?php if($errors->any()): ?> 
		<?php
		$team->name = Request::old('name');
		$team->description = Request::old('description');
		?>
	<?php endif; ?>
 
 	<?php if($team->id > 0): ?>
    <form action="<?php echo e(route('teams.update',$team->id)); ?>" method="POST">
   <?php else: ?>
    <form action="<?php echo e(route('parents.teams.store', $team->parent)); ?>" method="POST">
   <?php endif; ?> 
   <?php echo csrf_field(); ?>
 	<?php if($team->id > 0): ?>
     <?php echo method_field('PUT'); ?>
	<?php endif; ?>   
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Parent:</strong>
                    <input type="text" name="parent" value="<?php echo e($team->parent); ?>" class="form-control" placeholder="Title">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Title:</strong>
                    <input type="text" name="name" value="<?php echo e($team->name); ?>" class="form-control" placeholder="Title">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" 
                    name="description" placeholder="Detail"><?php echo $team->description; ?></textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
   
    </form>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>  
<?php /**PATH /var/www/html/netpolgar/resources/views/teams/edit.blade.php ENDPATH**/ ?>