<div clss="pollResult>">
	@if ($poll->status == 'vote')
	<h3>{{ __('poll.subResult') }}</h3>
	@else
	<h3>{{ __('poll.result') }}</h3>
	@endif
	@if ($info->memberCount > 0)
		<h4>Leadott szavazatok száma: {{ $info->voteCount }} / {{ $info->memberCount }}
		 -- {{ round($info->voteCount / $info->memberCount * 100) }}%</h4>
		@if (round($info->voteCount / $info->memberCount * 100) >= $poll->config->valid)
			<p>Érvényes szavazás</p>
		@endif
	@endif 
	<div class="row">
		<div style="display:inline-block; width: 350px">
			<canvas id="myCanvas"></canvas>
		</div>
		<div style="display:inline-block; width: 350px">
			<ul id="labels">
			</ul>
		</div>
	</div>
	@if (isset($voteInfo->html))
	<div class="row">&nbsp;</div>
	<div class="row">
	{!! $voteInfo->html !!}
	<p>
	A kiértékelés Condorcet - Shulze eljárással készült.
	<a href="https://en.wikipedia.org/wiki/Schulze_method" target="_new">
		<em class="fas fa-hand-point-right"></em>lásd itt. 
	</a>
	</p>
	</div>
	@endif

	<script type="text/javascript">
		var myCanvas = document.getElementById("myCanvas");
		myCanvas.width = 300;
		myCanvas.height = 300;
 		var ctx = myCanvas.getContext("2d");
 		var colors = [
            'red',
            'green',
            'yellow',
            'grey',
            'blue',
            'lime',
            'orange',
            'purple'
       ];
  		 var data = {!! JSON_encode($voteInfo->data) !!};
  		 var labels = {!! JSON_encode($voteInfo->labels) !!};

		function drawLine(ctx, startX, startY, endX, endY){
		    ctx.beginPath();
		    ctx.moveTo(startX,startY);
		    ctx.lineTo(endX,endY);
		    ctx.stroke();
		}	
		
		function drawArc(ctx, centerX, centerY, radius, startAngle, endAngle){
		   ctx.beginPath();
    		ctx.arc(centerX, centerY, radius, startAngle, endAngle);
    		ctx.stroke();
		}
		
		function drawPieSlice(ctx,centerX, centerY, radius, 
			startAngle, endAngle, color ) {
    		ctx.fillStyle = color;
    		ctx.beginPath();
    		ctx.moveTo(centerX,centerY);
    		ctx.arc(centerX, centerY, radius, startAngle, endAngle);
    		ctx.closePath();
    		ctx.fill();
		}
		
		var colorIndex = 0;
		var startAngle = 0;
		var endAngle = 0;
		var i = 0;
		var total = 0;
		var s = '';
		for (i=0; i<data.length; i++) {
			total += data[i];		
		}
		for (i=0; i<data.length; i++) {
			endAngle = startAngle + ((Math.PI*2) / total) * data[i];
			drawPieSlice(ctx,150, 150, 150, 
				startAngle, endAngle, colors[colorIndex] ); 
			startAngle = endAngle;
			s = $('#labels').html();
			s += '<li><var style="background-color:'+colors[colorIndex]+'">&nbsp;&nbsp;</var>'+labels[i]+'</li>';
			$('#labels').html(s); 
			colorIndex++;
			if (colorIndex >= colors.length) {
				colorIndex = 0;			
			}		
		}
		
		function reszletekClick() {
			$('#eredmenyInfo').toggle();		
		}
	</script>
</div>