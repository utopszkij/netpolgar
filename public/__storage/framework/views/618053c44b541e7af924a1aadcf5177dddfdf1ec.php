<?php 

function validate_gravatar($email) {
	$hash = md5($email);
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = FALSE;
	} else {
		$has_valid_avatar = TRUE;
	}
	return $has_valid_avatar;
}

if (Auth::user()) {
	$user = Auth::user();
	if (Auth::user()->profile_photo_path == '') {
		if (validate_gravatar($user->email)) {
			$avatar = 'https://gravatar.com/avatar/'.md5($user->email);
		} else {
			$avatar = str_replace('/storage/','/storage/app/public/',
				$user->profile_photo_url);
		}	
	} else {
		$avatar = str_replace('/storage/','/storage/app/public/',
			$user->profile_photo_url);
	}	
	Auth::user()->avatar = $avatar;
}
?>
  
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo e(URL::to('/')); ?>">
		<img src="<?php echo e(URL::to('/')); ?>/img/logo.png" class="logo" />    
    	Netpolgár
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" 
          	href="<?php echo e(URL::to('/parents/0/teams')); ?>">
          	<?php echo e(__('navigation.groups')); ?>

          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
          	<?php echo e(__('navigation.Projects')); ?>

          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
          	<?php echo e(__('navigation.market')); ?>

          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
            _data-bs-toggle="dropdown" _aria-expanded="false"
            onclick="$('#msgDropdown').toggle(); false">
            <?php echo e(__('navigation.kommunikation')); ?>

          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="msgDropdown">
            <li><a class="dropdown-item" href="#"><?php echo e(__('navigation.privatmsg')); ?></a></li>
            <li><a class="dropdown-item" href="#"><?php echo e(__('navigation.forum')); ?></a></li>
            <li><a class="dropdown-item" href="#"><?php echo e(__('navigation.voks')); ?></a></li>
            <li><a class="dropdown-item" href="#"><?php echo e(__('navigation.rules')); ?></a></li>
          </ul>
        </li>
      </ul>
      <ul class="navbar-nav mb-2 mb-lg-0">
      	<?php if(auth()->guard()->check()): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
            _data-bs-toggle="dropdown"  _aria-expanded="false"
            onclick="$('#loginDropdown0').toggle(); false">
            <img src="<?php echo e($avatar); ?>" class="logo" />
            <?php echo e(Auth::user()->name); ?>

          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="loginDropdown0"
            style="right:20px">
            <li><a class="dropdown-item" href="<?php echo e(URL::to('/user/profile')); ?>">
            	<?php echo e(__('navigation.profile')); ?>

            </a></li>
            <li>
            	<form method="post" id="logoutForm" action="<?php echo e(URL::to('/logout')); ?>">
            		<?php echo csrf_field(); ?>
             		<a href="#" onclick="$('#logoutForm').submit()"><?php echo e(__('navigation.logout')); ?></a>
             	</form>	
            </li> 
          </ul>
        </li>
      	<?php else: ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
          	_data-bs-toggle="dropdown" _aria-expanded="false" 
          	onclick="$('#loginDropdown1').toggle(); false">
            <?php echo e(__('navigation.enter')); ?>

          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="loginDropdown1" 
          style="right:20px;">
            <li><a class="dropdown-item" href="<?php echo e(URL::to('/login')); ?>">
            	<?php echo e(__('navigation.login')); ?>

            	</a></li>
            <li><a class="dropdown-item" href="<?php echo e(URL::to('/register')); ?>">
            	<?php echo e(__('navigation.register')); ?>

            	</a></li>
          </ul>
        </li>
      	<?php endif; ?>
	  </ul>      
    </div>
  </div>
</nav>

<?php /**PATH /var/www/html/netpolgar/resources/views/navigation-menu.blade.php ENDPATH**/ ?>