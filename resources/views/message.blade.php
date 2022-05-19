@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
    	<li>{{ $error}}</li>
    @endforeach
</div>
@endif
<div id="popup">
<div id="popupBg">
</div>
<div id="popupBody">
		<div id="popupTxt">popup szöveg</div>
		<div id="popupBtns">
			<button id="btnYes" type="button" class="btn btn-primary">Igen</button>	
			<button id="btnYesDanger" type="button" class="btn btn-danger">Igen</button>	
			<button id="btnNo" type="button"
			onclick="$('#popup').hide()" 
			class="btn btn-secondary">Nem</button>	
			<button id="btnClose" type="button"
			onclick="$('#popup').hide()" 
			class="btn btn-secondary">Bezár</button>	
		</div>
</div>
</div>

<div id="waiting" class="row"
   style="display:none; position:fixed; z-index:100; width:100%; height:100%">
   <div class="col-12">
   	<img src="/img/waiting-icon.gif" 
   	style="width:10%; margin:20% 20% 20% 45%;" />
   </div>
</div>

<script type="text/javascript">
	function popupTxt(txt) {
		$('#popupTxt').html(txt);
		$('#btnYes').hide();	
		$('#btnNo').hide();	
		$('#btnYesDanger').hide();	
		$('#btnClose').show();
		$('#popup').show();	
	}
	
	function popupConfirm(txt, fun, danger) {
		$('#popupTxt').html(txt);
		$('#btnYes').click(fun);
		$('#btnYesDanger').click(fun);
		if (danger) {
			$('#btnYesDanger').show();
			$('#btnYes').hide();
		} else {		
			$('#btnYesDanger').hide();
			$('#btnYes').show();
		}		
		$('#btnNo').show();	
		$('#btnClose').hide();
		$('#popup').show();	
	}
	
	function popupClose() {
		$('#popup').hide();	
	}

	function help(name) {
		var url = "{{ \URL::to('/help/page')}}/"+name;
		if (window.innerWidth < 575) {
			url += "?m=p";
		} else {
			url += "?m=d";
		}
		window.open(url, '_help').focus();
		return false;
	}
	
	function showWaiting() {
		console.log('showWaiting');
		$('#waiting').show();
		return true;
	}

	function hideWaiting() {
		$("#waiting").hide();
		return true;
	}
	
	$(function() {
		// türelem kérő animáció az a és button elemekre
		// a profil képernyőn és login képernyőn ez nem kell
		if ((window.location.href.search('user/profile') >= 0) |
		    (window.location.href.search('/login') >= 0)) {
			return;
		}
		/* firefox és safari böngészőkben bajt okoz  :(  
			ugyanis ezek a browser back használata esetén 
			a cache -ból töltenek be ahol a div #waiting show állapotban van,
			és ezt az alábbi script részt pedig nem hajtják újra végre.
		*/	
		var userAgent = navigator.userAgent;
		var ci = userAgent.indexOf('Chrome');
		var si = userAgent.indexOf('Safari');	
		if ((ci > 0) & 
		    ((si <= 0) | (ci < si))) {
			var w = $('a');
			for (var i=0; i<w.length; i++) {
				if (w[i].onclick == undefined) {
					w[i].onclick = showWaiting; 
				}			
			}
			var w = $('button');
			for (var i=0; i<w.length; i++) {
				if (w[i].onclick == undefined) {
					w[i].onclick = showWaiting;
				}			
			}
			}
		$("#waiting").hide();
	});
</script>

</div>
