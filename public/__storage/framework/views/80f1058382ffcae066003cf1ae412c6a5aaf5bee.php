<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
      <div class="pageContainer row construction">
      	<div style="text-align:center">
      	  <br /><br /><br />
           <img src="<?php echo e(URL::to('/')); ?>/img/construction.png"
           style="display:inline-block; height:30%" 
           alt="construction" title="construction" />
           <br />
           Sajnos ez még nincs kész  :(
          </div>      
      </div>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/netpolgar/resources/views/construction.blade.php ENDPATH**/ ?>