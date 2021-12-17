<div clss="pollResult>">
	@if ($poll->status == 'vote')
	<h3>{{ __('poll.subResult') }}</h3>
	@else
	<h3>{{ __('poll.result') }}</h3>
	@endif

	<div id="chart"></div>
	<div id="chartLabels"></div>

	<script src="{{ \URL::to('/js/jquery.piegraph.js') }}"></script>
	<script>
		$("#chart").chart({
  			data :new Array(40, 40, 45, 30, 25),
  			labels :new Array("A","B","C","D","E"),
  			width : window.innerWidth * 0.5,
  			height : window.innerWidth * 0.25,
  			unit: '%'
		}); 
	</script>
</div>