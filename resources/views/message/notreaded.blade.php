<?php 

$member = false;

// include_once \Config::get('view.paths')[0].'/minimarkdown.php';
use App\Models\Minimarkdown;
use App\Models\Avatar;

foreach ($data as $item) {
    $item->cavatar = Avatar::userAvatar($item->cavatar, $item->cemail);
}

?>

<x-guest-layout>
<div id="messages" class="pageContainer row messagesTree">
	<p>&nbsp;</p>
	<h2>{{ __('messages.notreaded') }}</h2>
	
	@foreach ($data as $item)
	<div class="row messages">
		<div class="col-12 msg">
				<a href="{{ \URL::to('/message/tree/'.$item->parent_type.'/'.$item->parent) }}">
				    <h3>{{ $item->pname }}</h3>
					<img src="{{ $item->cavatar }}" class="avatar" />{{ $item->cname }}
					&nbsp;{{ $item->created_at}} <br />
					@if ($item->rid > 0)
					  <strong>{{ $item->rname }}</strong>:&nbsp;
					@endif
					{!! Minimarkdown::miniMarkdown($item->value) !!} 					
				</a>
		</div>
	</div>
	@endforeach
	@if (count($data) <= 0)
    <p>{{ __('messages.notData') }}</p>
    @else
    <p>{{ __('messages.notreadedHelp') }}</p>
    @endif
    {!! $data->links() !!}
	
</div>
</x-guest-layout>
