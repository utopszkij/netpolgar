<x-guest-layout>  
	<div id="memberContainer">
    <div class="row" style="margin-top: 5rem;">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>
                    <a href="/{{ $parent_type }}/{{ $parent->id }}">
                    	<em class="fas fa-hand-point-right"></em>{{ $parent->name}}
                    </a>
	                {{ __('member.members') }}
                </h2>
            </div>
        </div>
    </div>

    <table class="table table-bordered indexTable">
    	<thead>
        <tr>
            <th>{{ __('member.status') }}</th>
            <th>{{ __('member.rank') }}</th>
            <th>{{ __('member.name') }}</th>
            <th></th>
        </tr>
      </thead>
      <tbody>  
        @foreach ($data as $key => $value)
        @php 
			 if ($value->profile_photo_path == '') {
				$value->profile_photo_path = 'https://gravatar.com/avatar/'.
				   md5($value->email).
				   '?d='.urlencode('https://www.pinpng.com/pngs/m/341-3415688_no-avatar-png-transparent-png.png');
			 } else {
			 	$value->profile_photo_path = \URL::to('/storage/app/public').'/'.$value->profile_photo_path;
			 }        
        @endphp
        <tr>
            <td>{{ __('member.'.$value->status) }}</td>
            <td>{{ __('member.'.$value->rank) }}</td>
            <td>
            	<a href="{{ \URL::to('/member/'.$value->id) }}">
            	<img class="avatar" src="{{ $value->profile_photo_path }}" class="logo" alt="logo" title="logo" />
            	{{ $value->name }}
            	</a>
            </td>
            <td>
            @if (count($info->userRank) > 0) 
            	@if (($value->status == 'proposal') & ($value->rank == 'member'))
            	   @if ($info->userLiked[$key])
            	   <em class="fas -fa-check"></em>	
            	   @endif
            		<a href="{{ \URL::to('/like/members/'.$value->id) }}">
            			<em class="fas fa-thumbs-up"></em>
            			{{ __('member.like') }}
            		</a>&nbsp;
            		<a href="{{ \URL::to('/likeinfo/members/'.$value->id) }}">
            			{{ $info->likeCount[$key] }} / {{ $info->likeReqMember }}
					</a>
            	@endif
            	@if (($value->status == 'proposal') & ($value->rank != 'member'))
            	   @if ($info->userLiked[$key])
            	   <em class="fas -fa-check"></em>	
            	   @endif
            		<a href="{{ \URL::to('/like/members/'.$value->id) }}">
            			<em class="fas fa-thumbs-up"></em>
            			{{ __('member.like') }}
            		</a>&nbsp;
            		<a href="{{ \URL::to('/likeinfo/members/'.$value->id) }}">
            			{{ $info->likeCount[$key] }} / {{ $info->likeReqRank }}
            		</a>a>	
            	@endif
            	@if (($value->status == 'active') & ($value->rank == 'member'))
            	   @if ($info->userDisLiked[$key])
            	   <em class="fas fa-check"></em>	
            	   @endif
            		<a href="{{ \URL::to('/dislike/members/'.$value->id) }}">
            			<em class="fas fa-thumbs-down"></em>
            			{{ __('member.disLikeMember') }}
            		</a>&nbsp;
            		<a href="{{ \URL::to('/likeinfo/members/'.$value->id) }}">
	           			{{ $info->disLikeCount[$key] }} / {{ $info->disLikeReqMember }}
	           		</a>	
            	@endif
            	@if (($value->status == 'active') & ($value->rank != 'member'))
            	   @if ($info->userDisLiked[$key])
            	   <em class="fas fa-check"></em>	
            	   @endif
            		<a href="{{ \URL::to('/dislike/members/'.$value->id) }}">
            			<em class="fas fa-thumbs-down"></em>
            			{{ __('member.disLikeRank') }}
            		</a>&nbsp;
            		<a href="{{ \URL::to('/likeinfo/members/'.$value->id) }}">
	           			{{ $info->disLikeCount[$key] }} / {{ $info->disLikeReqRank }}
	           		</a>	
            	@endif
            @endif

            @if (\Auth::user())
            @if (($value->user_id == \Auth::user()->id) & ($value->rank != 'member'))
        		<form action="{{ URL::to('/member/doexit') }}">
        		<input type="hidden" name="parent_type" value="teams" />
        		<input type="hidden" name="parent" value="{{ $parent->id }}" />
        		<input type="hidden" name="rank" value="{{ $value->rank }}" />
        		<button type="submit" class="btn btn-danger" title="Csatlakozok a csoporthoz">
        				<em class="fas fa-ban"></em>
						{{ __('member.signout') }}        				
        		</button>
        		</form>
            @endif
            @endif
            
            </td>
        </tr>
        @endforeach
       </tbody> 
    </table>
    <p class="help">További részletekért kattints a névre!</p>
    {!! $data->links() !!}
    
   
    @if (count($info->userRank) > 0)
    <div class="row">
    	<form methid="get" action="">
    	<input type="hidden" name="parent_type" value="{{ $parent_type}}" /> 
    	<input type="hidden" name="parent" value="{{ $parent->id }}" /> 
		<div class="form-group">
			<select name="rank">
				@foreach ($info->ranks as $key => $value)
				<option value="{{ $value }}">{{ __('member.'.$value) }}</option>
				@endforeach
			</select>		
			<button type="submit" class="btn btn-primary">
				{{ __('member.aspirantRank') }}			
			</button>
		</div>
		</form>		    
    </div> 
    @endif

    
  </div>        
</x-guest-layout>  
