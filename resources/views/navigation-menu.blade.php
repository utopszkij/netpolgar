@php 

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
	$notReadedCount = \App\Models\Message::getNotreadedCount();
} else {
	$notReadedCount = 0;
}
@endphp
  
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ URL::to('/') }}">
		<img src="{{ URL::to('/') }}/img/logo.png" class="logo" />    
    	Netpolgár
    </a>
	@auth
	<img src="{{ $avatar }}" class="loggedAvatar1" style="height:32px" />
	@endif

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
      aria-expanded="false" aria-label="Toggle navigation" onclick="false">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" 
          	href="{{ URL::to('/parents/0/teams') }}">
          	{{ __('navigation.groups') }}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" 
          	href="{{ URL::to('/products/list/0') }}">
          	{{ __('navigation.market') }}
          </a>
        </li>
        @if ($notReadedCount > 0)
        <li class="nav-item  notreaded">
          <a class="nav-link" aria-current="page" 
          	href="{{ URL::to('/message/notreaded') }}">
          	{{ $notReadedCount }} db {{ __('navigation.notreaded') }}
          </a>
        </li>
        @endif
      </ul>
      <ul class="navbar-nav mb-2 mb-lg-0">
      	@auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
            _data-bs-toggle="dropdown"  _aria-expanded="false"
            onclick="$('#loginDropdown0').toggle(); false">
            <img src="{{ $avatar }}" class="logo" />
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="loginDropdown0"
            style="right:20px">
            <li><a class="dropdown-item" href="{{ URL::to('/user/profile') }}">
            	{{ __('navigation.profile') }}
           		</a>
           	</li>
            <li><a class="dropdown-item" href="{{ URL::to('/message/tree/users/'.\Auth::user()->id) }}">
            	{{ __('navigation.messages') }}
           		</a>
           	</li>
            <li><a class="dropdown-item" href="{{ URL::to('/products/listbyuser/'.\Auth::user()->id) }}">
            	{{ __('navigation.products') }}
           		</a>
           	</li>
            <li><a class="dropdown-item" href="{{ URL::to('/orders/list/?producer_type=users&producer='.\Auth::user()->id) }}">
            	{{ __('navigation.orders') }}
           		</a>
           	</li>
            <li><a class="dropdown-item" href="{{ URL::to('/account/list/users/'.\Auth::user()->id) }}">
            	{{ __('navigation.account') }}
           		</a>
           	</li>
            <li><a class="dropdown-item" href="{{ URL::to('/users/'.\Auth::user()->id.'/files') }}">
            	{{ __('navigation.files') }}
           		</a>
           	</li>
           	
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
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
          	_data-bs-toggle="dropdown" _aria-expanded="false" 
          	onclick="$('#loginDropdown1').toggle(); false">
            {{ __('navigation.enter') }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="loginDropdown1" 
          style="right:20px;">
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
