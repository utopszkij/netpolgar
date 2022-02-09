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
                <h2>Csoportok</h2>
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

    <?php if(($info->status == 'active') & ($info->userRank != '')): ?>
    <div class="row buttons">
       <a class="btn btn-primary" 
         href="<?php echo e(route('parents.teams.create',["parent" => $parent])); ?>">
        	<em class="fas fa-plus"></em>Javaslat új alcsoport létrehozására
       </a>
    </div>
    <?php endif; ?>
     
    <?php if(count($data) > 0): ?>    
    <div class="row help">
		Részletekért és további lehetőségért kattints a csoport nevére!				
    </div>
    <table class="table table-bordered">
        <tr>
            <th></th>
            <th>Név</th>
            <th>Leírás</th>
            <th style="width:170px"></th>
        </tr>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if ($value->avatar == '') $value->avatar = URL::to('/').'/img/team.png'; ?>
        <tr>
            <td><?php echo e(++$i); ?></td>
            <td>
            	<a href="<?php echo e(route('teams.show', $value->id)); ?>">
            	<img src="<?php echo e($value->avatar); ?>" class="logo" alt="logo" title="logo" />
            	<?php echo e($value->name); ?>

            	</a>
            </td>
            <td><?php echo e(\Str::limit($value->description, 100)); ?></td>
            <td>
                <form action="<?php echo e(route('teams.destroy', $value->id)); ?>"
                	  id="teamForm"	
                	  method="POST">   
                    <?php if($info->userRank == 'admin'): ?>
                    <a class="btn btn-primary" title="módosítás"
                    		href="<?php echo e(route('teams.edit', $value->id)); ?>">
                    		<em class="fas fa-edit"></em>
                    </a>   
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <?php if($info->userRank == 'admin'): ?>
                    <button type="button" class="btn btn-danger"
                    onclick="delClick()" 
                    title="törlés">
                    <em class="fas fa-ban"></em>
                    </button>
                    <?php endif; ?>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php else: ?>
    <div>Nincsenek csoportok.</div>
    <?php endif; ?>  
    <?php echo $data->links(); ?>

    
    <script>
		function delClick() {
			if (confirm('Biztos törölni akarod ezt a csoportot?')) {
				document.getElementById('teamForm').submit();			
			}		
		}   
    </script>
  </div>        
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>  
<?php /**PATH /var/www/html/netpolgar/resources/views/teams/index.blade.php ENDPATH**/ ?>