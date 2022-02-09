<?php 
// ha csak egyetlen egy user van, akkor az legyen sysadmin
$table = \DB::table('users');
$darab = $table->count();
if ($darab == 1) {
    $user = $table->orderBy('id')->first();
    $table->where('id','=',$user->id);
    $table->update(["current_team_id" => 0]);
}
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
    	<?php echo $__env->make('layouts/htmlhead', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>
    <body class="antialiased">
    	<div>
	    	<?php echo $__env->make('navigation-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    	</div>
		<main class="vueRun">		
	    	<?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	    	<div>
	    		<p>&nbsp;</p>
				<h2>VUE RUNNER</h2>
				<p>Bejelentkezve: 
				<?php
				if (Auth::user()) { 
					echo Auth::user()->name;
				} else {
					echo 'Nincs bejelentkezve';				
				}
				?>

<div id="components-demo">
  <button-counter></button-counter>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.min.js"></script>
<script src="http://localhost/netpolgar/resources/views/vueRun.js"></script>

		</main>
      <div>
	        <?php echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	   </div>    
    </body>
</html>
<?php /**PATH /var/www/html/netpolgar/resources/views/vueRun.blade.php ENDPATH**/ ?>