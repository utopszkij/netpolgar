<x-guest-layout>
<div id="messagProtest" class="pageContainer row protest">
	@php
		$moderatorsArray = [];
		foreach ($moderators as $moderator) {
			$moderatorsArray[] = $moderator->user_id;
		} 
		$txt = 'Kifogásolom az ('.$myMessage->id.') üzenet tartalmát!'."\n\n".$myMessage->value."\n\n".'Kifogásom:';
	@endphp	
	<p> </p>
	<h2>{{ __('messages.protestForm') }}</h2>
	<form method="post" action="{{ \URL::to('/message/saveprotest') }}">
	 	@csrf
		<input type="hidden" name="messageId" value="{{ $myMessage->id }}" />
		<input type="hidden" name="moderators" value="{{ implode(',',$moderatorsArray) }}" />
    	<textarea id="value" name="txt" cols="60" rows="10" style="width:70%">{!! $txt !!}</textarea>
    	<button type="submit" class="btn btn-primary">
    		<em class="fas fa-paper-plane"></em>{{ __('messages.send') }}
    	</button>
	</form>
</div>
</x-guest-layout>
