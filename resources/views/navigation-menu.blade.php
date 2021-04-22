@php 
if (Auth::user()) {
	$avatar = str_replace('storage/','/storage/app/public/',Auth::user()->profile_photo_url);
}
@endphp
  
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ URL::to('/') }}">
		<img src="{{ URL::to('/') }}/img/logo.png" class="logo" />    
    	Netpolgár
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">
          	{{ __('navigation.groups') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
          	{{ __('navigation.Projects') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
          	{{ __('navigation.market') }}
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('navigation.kommunikation') }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">{{ __('navigation.privatmsg') }}</a></li>
            <li><a class="dropdown-item" href="#">{{ __('navigation.forum') }}</a></li>
            <li><a class="dropdown-item" href="#">{{ __('navigation.voks') }}</a></li>
            <li><a class="dropdown-item" href="#">{{ __('navigation.rules') }}</a></li>
          </ul>
        </li>
      </ul>
      <ul class="navbar-nav mb-2 mb-lg-0">
      	@auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $avatar }}" class="logo" />
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="{{ URL::to('/user/profile') }}">
            	{{ __('navigation.profile') }}
            </a></li>
            <li>
            	<form method="post" id="logoutForm" action="{{ URL::to('/logout') }}">
            		@csrf
             		<a href="#" onclick="$('#logoutForm').submit()">{{ __('navigation.logout') }}</a>
             	</form>	
            </li> 
          </ul>
        </li>
      	@else
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('navigation.enter') }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="{{ URL::to('/login') }}">
            	{{ __('navigation.login') }}
            	</a></li>
            <li><a class="dropdown-item" href="{{ URL::to('/register') }}">
            	{{ __('navigation.register') }}
            	</a></li>
          </ul>
        </li>
      	@endauth
	  </ul>      
    </div>
  </div>
</nav>

