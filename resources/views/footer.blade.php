 <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row footer-info">

				<div class="col-sm-6  footer-links">
					<strong><em class="fa fa-lock"></em> GDPR</strong><br />
					<ul>
					<li><i class="bx bx-chevron-right"></i>
						<a href="{{ \URL::to('/policy') }}">
						{{ __('footer.privacy-policy') }}
						</a>
					</li>
					<li><i class="bx bx-chevron-right"></i>
						<a href="{{ \URL::to('/terms') }}">
						{{ __('footer.term-of-service') }}
						</a>
					</li>
				</div>
				<div class="col-sm-6  footer-links">
					<ul>
					<li><i class="bx bx-chevron-right"></i>
						<a href="{{ \URL::to('/impressum') }}">
						<em class="fa fa-info-circle"></em> {{ __('footer.impressum') }}
						</a></li>
					<li><i class="bx bx-chevron-right"></i>
						<a href="https://opensource.org/licenses/MIT" target="_new">
						<em class="fa fa-copyright"></em> {{ __('footer.licence') }}
						</a></li>
					<li><i class="bx bx-chevron-right"></i>
						<a href="https://github.com/utopszkij/netpolgar" target="_new">
						<em class="fa fa-code"></em> {{ __('footer.source') }}
						</a></li>
					<li><i class="bx bx-chevron-right"></i>
						<a href="mailto::tibor.fogler@gmail.com?subject=netpolgar hiabjelzés"
						   onclick="$('#waiting').hide()">
						<em class="fa fa-bug"></em> {{ __('footer.bugreport') }}
						</a></li>
					</ul>
				</div>
			</div>
			<div class="row">
	            <div class="col-sm-6 footer-links">
		        </div>    
	            <div class="col-sm-6 footer-links">
		            <div class="social-links mt-3">
		              <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
		              <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
		              <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
		              <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
		              <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
		            </div>
          		</div>
			</div>
		</div>
	</div>
	<div id="cookieEnable" style="display:none">
		{{ __('footer.enableLabel') }}
		&nbsp;		
		<a href="{{ \URL::to('/policy') }}" style="color:blue">
		{{ __('footer.seeThis') }}
		</a>
		<br />	
		<a class="btn btn-secondary" href="{{ URL::current() }}" 
			onclick="setCookie('netpolgarCookieEnable',1,20)">
			{{ __('footer.enableBtn') }}
		</a>
	</div>
	<div id="cookieDisable" style="display:none">
		{{ __('footer.disableLabel') }}
		<a class="btn btn-secondary"  href="{{ URL::current() }}" 
			onclick="setCookie('netpolgarCookieEnable',0,20)">
			{{ __('footer.disableBtn') }}
		</a>
	</div>
</footer>
<script type="text/javascript">
	function setCookie(cname, cvalue, exdays) {
	  var d = new Date();
	  d.setTime(d.getTime() + (exdays*24*60*60*1000));
	  var expires = "expires="+ d.toUTCString();
	  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}
	function getCookie(cname) {
	  var name = cname + "=";
	  var decodedCookie = decodeURIComponent(document.cookie);
	  var ca = decodedCookie.split(';');
	  for(var i = 0; i <ca.length; i++) {
	    var c = ca[i];
	    while (c.charAt(0) == ' ') {
	      c = c.substring(1);
	    }
	    if (c.indexOf(name) == 0) {
	      return c.substring(name.length, c.length);
	    }
	  }
	  return "";
	}
	$(function() {
		if (getCookie('netpolgarCookieEnable') == 1) {
			$('#cookieDisable').show();
		} else {
			$('#cookieEnable').show();
		}
	});
</script>

<div><pre>
<?php 
// task info tárolása a sessionba a hibajelentéshez
request()->session()->forget('taskInfo');
$taskInfo = new \stdClass();
$taskInfo->REQUEST_URI = $_SERVER['REQUEST_URI'];
$taskInfo->REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$taskInfo->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$taskInfo->REQUESTS = request()->all();
$taskInfo->SESSIONS = request()->session()->all();
$taskInfo->USER = \Auth::user();
request()->session()->put('taskInfo',$taskInfo);
?>
</pre></div>

