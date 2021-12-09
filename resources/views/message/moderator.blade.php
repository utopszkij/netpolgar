<x-guest-layout>
<div id="messages" class="pageContainer row moderator">

	<h2>{{ __('messages.moderator') }}</h2>
	<form method="post" action="{{ \URL::to('/message/store') }}">
	 	@csrf
		<input type="hidden" name="parent_type" value="{{ $myMessage->parent_type }}" />
		<input type="hidden" name="parent" value="{{ $myMessage->parent }}" />
		<input type="hidden" name="messageId" value="{{ $myMessage->id }}" />
		<input type="hidden" name="backURL" value="{{ $backURL }}" />
    	<textarea id="value" name="value" cols="60" rows="4" style="width:70%">{!! $myMessage->value !!}</textarea>
    	<p>{{ __('messages.moderator_info') }}</p>
    	<textarea id="value" name="moderator_info" cols="60" rows="4" style="width:70%"></textarea>
    	<button type="submit" class="btn btn-primary">
    		<em class="fas fa-paper-plane"></em>{{ __('messages.send') }}
    	</button>
	</form>
</div>
</x-guest-layout>
