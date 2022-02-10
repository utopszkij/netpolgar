<x-guest-layout>  
	@php
	function avatar($profile_photo_path, $email) {
		 if ($profile_photo_path == '') {
			$result = 'https://gravatar.com/avatar/'.
			   md5($email).
			   '?d='.urlencode('https://www.pinpng.com/pngs/m/341-3415688_no-avatar-png-transparent-png.png');
		 } else {
		 	$result = '/'.$profile_photo_path;
		 } 
		 return $result;       
	}
	@endphp
	<div id="likeInfoContainer">
    	<h2><em class="fas fa-thumbs-up"></em> / <em class="fas fa-thumbs-down"></em> {{ __('like.title') }}</h2>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>
                        <a href="/{{ $parentType }}/{{ $parent->id }}">
                        	 <em class="fas fa-hand-point-right"></em>
                            {{ $parent->name }}
                        </a> 
                    </h2>
                    <h3>{{ __('like.'.$parentType) }}</h3>
                </div>
            </div>
        </div>
    
        <table class="table table-bordered" style="width:auto">
            <tr>
                <th><em class="fas fa-thumbs-up"></em></th>
                <th><em class="fas fa-thumbs-down"></em></th>
            </tr>
            <tr>
            	<td>
                	@foreach ($likeUsers as $key => $user)
                        <img class="avatar" 
                        src="{{ avatar($user->profile_photo_path, $user->email) }}" />
                        &nbsp;
                       	{{ $user->name }}
                        <br />
                    @endforeach
                </td>
                <td>
                	@foreach ($disLikeUsers as $key => $user)
                        <img class="avatar" 
                        src="{{ avatar($user->profile_photo_path, $user->email) }}" />
                        &nbsp;{{ $user->name }}
                        <br />
                    @endforeach
                </td>
            </tr>
        </table>
        <a class="btn btn-primary" href="{{ \URL::previous() }}">
        	<em class="fas fa-reply"></em>{{ __('like.back') }}</a> 
  </div>        
</x-guest-layout>  
