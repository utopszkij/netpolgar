<?php		
		use App\Models\Minimarkdown;
?>
		<div class="msg level{{ $treeItem->level }}">
			@php 
			// params: $treeItem, $parentType, $parent, $parentId, $member 
			@endphp
			<div class="msgHeader">
				<a href="{{ \URL::to('/member/user/'. $treeItem->userId) }}">
					<img class="avatar" src="{{ $treeItem->avatar }}" />&nbsp;
					{{ $treeItem->creator }}&nbsp;
				</a>	
				{{ $treeItem->time }}&nbsp;
				@if (($moderator) | (\Auth::check() & ($treeItem->user_id == \Auth::user()->id)))
					<a href="{{ \URL::to('/message/moderal/'.$treeItem->id) }}"><em class="fas fa-edit"></em></a>
				@endif
			</div>
			<div class="msgBody">
				@if ($treeItem->replyTo[1] != '')
				<div class="replyTo">
					<a href="{{ \URL::to('/member/user/'.$treeItem->replyTo[2]) }}">
						{{ $treeItem->replyTo[1] }}
					</a>
				</div>
				@endif	
			  	{!! Minimarkdown::miniMarkdown($treeItem->text) !!} 
				@if ($treeItem->moderatorInfo != '')
				<div class="moderatorInfo">{{ $treeItem->moderatorInfo }}</div>
				@endif			  	
			</div>
			<div class="msgFooter">
				<a href="{{ \URL::to('/like/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					<em class="fas fa-thumbs-up"></em>
				</a>&nbsp;
				<a href="{{ \URL::to('/likeinfo/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					{{ $treeItem->likeCount }}</em>
				</a>&nbsp;
				<a href="{{ \URL::to('/dislike/messages/'.$treeItem->id) }}" class="{{ $treeItem->disLikeStyle }}">
					<em class="fas fa-thumbs-down"></em>
				</a>&nbsp;
				<a href="{{ \URL::to('/likeinfo/messages/'.$treeItem->id) }}" class="{{ $treeItem->likeStyle }}">
					{{ $treeItem->disLikeCount }}</em>
				</a>&nbsp;
				@if (\Auth::check())
    				@if (($member) | 
    					 (($parentType == 'users') & ($parentId == \Auth::user()->id))
    					)
        				<a href="#" onclick="replyClick({{ $treeItem->id }})">
        					<em class="fas fa-reply"></em> Válasz
        				</a>&nbsp;&nbsp;&nbsp;
        				<a href="{{ \URL::to('/message/list/'. $parentType.'/'.$parent->id.'/'.$treeItem->id) }}">
        					{{ $treeItem->replyCount }} db válasz
        				</a>&nbsp;&nbsp;&nbsp;
        			@else
       					{{ $treeItem->replyCount }} db válasz
    				@endif
				@else
   					{{ $treeItem->replyCount }} db válasz
				@endif
				<a href="{{ \URL::to('/message/protest/'.$treeItem->id) }}">
					<var class="protest"><em class="fas fa-ban"></em>Jelentem</var>
				</a>&nbsp;
			</div>
			<div id="reply{{ $treeItem->id }}" style="display: none">
            	<form method="post" action="{{ \URL::to('/message/store') }}">
				 	@csrf
            		<input type="hidden" name="parent_type" value="{{ $parentType }}" />
            		@if ($parentType == 'users')
            			<input type="hidden" name="parent" value="{{ $treeItem->userId }}" />
            		@else
            			<input type="hidden" name="parent" value="{{ $parent->id }}" />
            		@endif
            		<input type="hidden" name="reply_to" value="{{ $treeItem->id }}" />
            		<input type="hidden" name="msg_type" value="" />
            		<em class="fas fa-reply"></em>
                	<textarea id="replyText{{ $treeItem->id }}" name="value" cols="60" rows="4" style="width:70%"></textarea>
                	<button type="submit" class="btn btn-primary">
                		<em class="fas fa-paper-plane"></em>{{ __('messages.send') }}
                	</button>
            	</form>
			</div>
		</div>	
