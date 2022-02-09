<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>  

   <?php if ($team->avatar == '') $team->avatar = URL::to('/').'/img/team.png'; ?>

	<div id="teamContainer">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2><?php echo e(__('team.details')); ?></h2>
            </div>
        </div>
    </div>
    
	<div class="row">
		<div class="col-1 col-md-2" id="teamMenu">
			<var id="subMenuIcon" class="subMenuIcon" onclick="toggleTeamMenu()">
				<em class="fas fa-caret-right"></em><br />			
			</var>
         <a href="<?php echo e(route('parents.teams.index', $team->parent)); ?>">
            <em class="fas fa-reply"></em>
            <span><?php echo e(__('team.back')); ?></span><br />
         </a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Tagok">
				<em class="fas fa-users"></em>
				<span><?php echo e(__('team.members')); ?></span><br />			
			</a>
			<a href="<?php echo e(route('parents.teams.index', $team->id)); ?>" title="Tagok">
				<em class="fas fa-sitemap"></em>
				<span><?php echo e(__('team.subGroups')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Projektek">
				<em class="fas fa-cogs"></em>
				<span><?php echo e(__('team.projects')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Termékek">
				<em class="fas fa-shopping-basket"></em>
				<span><?php echo e(__('team.products')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Beszégetés">
				<em class="fas fa-comments"></em>
				<span><?php echo e(__('team.comments')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Viták, szavazások">
				<em class="fas fa-retweet"></em>
				<span><?php echo e(__('team.debates')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Viták, szavazások">
				<em class="fas fa-balance-scale-left"></em>
				<span><?php echo e(__('team.polls')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Döntések">
				<em class="fas fa-check"></em>
				<span><?php echo e(__('team.decisions')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Fájlok">
				<em class="fas fa-folder-open"></em>
				<span><?php echo e(__('team.files')); ?></span><br />			
			</a>
			<a href="<?php echo e(URL::to('/construction')); ?>" title="Események">
				<em class="fas fa-calendar"></em>
				<span><?php echo e(__('team.events')); ?></span><br />			
			</a>
		</div>
		
		<div class="col-11 col-md-10" id="teamBody">
		    <div class="col-11 col-md-10 path" style="margin-top: 5px;">
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


	       <div class="col-11 col-md-10">
             <h3>
             	<?php if($team->status == 'active'): ?>
             	<em class="fas fa-check"></em>
             	<?php endif; ?>
             	<?php if($team->status == 'proposal'): ?>
             	<em class="fas fa-question"></em>
             	<?php endif; ?>
             	<?php if($team->status == 'closed'): ?>
             	<em class="fas fa-lock"></em>
             	<?php endif; ?>
             	<?php echo e($team->name); ?>

		          <?php if(in_array('admin',$info->userRank)): ?>
	             &nbsp;<a href="<?php echo e(route('teams.edit',['team' => $team->id])); ?> ">
						<em class="fas fa-edit" title="<?php echo e(__('team.edit')); ?>"></em>                
   	          </a>
   	          <?php endif; ?>
             </h3>
         </div>

        	<div class="col-11 col-md-10">
        		<?php echo e(__('team.'.$team->status)); ?>

        		<?php if(count($info->userRank) > 0): ?> 
        		,&nbsp;<?php echo e(implode(',',$info->userRank)); ?> vagy&nbsp;
        		<?php endif; ?>
        		<?php if((count($info->userRank) == 0) & ($team->status == 'active')): ?>
        			<a class="btn btn-primary" href="" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-sign-in-alt"></em>
						<?php echo e(__('team.signin')); ?>        				
        			</a>
        		<?php endif; ?>
        		<?php if((count($info->userRank) > 0) & ($team->status == 'active')): ?>
        			<a class="btn btn-danger" href="" title="Kilépek a csoportból">
        				<em class="fas fa-sign-out-alt"></em>
						<?php echo e(__('team.signout')); ?>        				
        			</a>
        		<?php endif; ?>

        		<?php if((count($info->userRank) > 0) & ($team->status == 'active')): ?>
        			<a class="btn btn-danger" href="" title="a csoport lezárását javaslom">
        				<?php if($info->userDisLiked): ?>
        				<em class="fas fa-check"></em>
        				<?php endif; ?>
        				<em class="fas fa-thumbs-down"></em>
        				(<?php echo e($info->disLikeCount); ?>/<?php echo e($info->disLikeReq); ?>)
						<?php echo e(__('team.dislike')); ?>

        			</a>
        		<?php endif; ?>
        		<?php if((count($info->userParentRank) > 0) & ($team->status == 'proposal')): ?>
        			<a class="btn btn-success" href="" title="a csoport aktiválását javaslom">
        				<?php if($info->userLiked): ?>
        				<em class="fas fa-check"></em>
        				<?php endif; ?>
        				<em class="fas fa-thumbs-up"></em>
        				(<?php echo e($info->likeCount); ?>/<?php echo e($info->likeReq); ?>)
						<?php echo e(__('team.like')); ?>

        			</a>
        		<?php endif; ?>
        </div>
        
	     <div class="col-11 col-md-10">
				<img src="<?php echo e($team->avatar); ?>" alt="logo" title="logo"
					style="float:right; width:25%" />        		
            <div style="width:70%">
            	<pre><?php echo $team->description; ?></pre>
            	<h4>Beállítások</h4>
					<div class="config" style="display:inline-block; width:500px">
						  tisztségek:  <?php echo e(implode(',',$team->config->ranks)); ?><br />	
						  <?php echo e($team->config->close); ?>

						  % támogatottság kell a csoport lezárásához,<br />
						  <?php echo e($team->config->memberActivate); ?>

						  fő támogató kell tag felvételéhez,<br />
						  <?php echo e($team->config->memberExclude); ?>

						  % támogatottság kell tag kizárásához,<br />
						  <?php echo e($team->config->rankActivate); ?>	
						  % támogatottság kell tisztség betöltéséhez,<br />
						  <?php echo e($team->config->rankClose); ?>

						  % támogatottság kell tisztség visszavonásához,<br />
						  <?php echo e($team->config->projectActivate); ?>

						  fő támogató kell projekt aktiválásához,<br />
						  <?php echo e($team->config->productActivate); ?> 
						  % támogatottság kell termék közzé tételéhez,<br />
						  <?php echo e($team->config->subTeamActivate); ?>

						  fő támogató kell alcsoport aktiválásához,<br />
						  <?php echo e($team->config->debateActivate); ?>

						  fő támogató kell eldöntendő vita inditásához
					</div>
            </div>
	     </div>
		</div> <!-- .row -->
	</div>    
    
   <script>
		function toggleTeamMenu() {
			var teamMenu = document.getElementById('teamMenu');
			if (teamMenu.style.width == "100%") {
				teamMenu.style.width="8.3%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="none";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-right"></em>';
			} else {
				teamMenu.style.width="100%";
				var spans = document.getElementsByTagName('span');
				var i = 0;
				for (i = 0; i < spans.length; i++) {
					spans[i].style.display="inline-block";
				} 	
				document.getElementById('subMenuIcon').innerHTML = '<em class="fas fa-caret-left"></em>';
			}
			return false;	
		}   
   </script> 
    
   
   </div>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>  
<?php /**PATH /var/www/html/netpolgar/resources/views/team/show.blade.php ENDPATH**/ ?>