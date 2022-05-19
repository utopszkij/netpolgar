<x-guest-layout>  

	@if ($errors->any())
		@php
		@endphp
	@endif

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2>Send news letter</h2>
            <h3>{{ $parentRec->name }}</h3>
            <p>
                @if ($total > 0)
                    sended: {{ $offset }}&nbsp; 
                    total: {{ $total }}
                @endif
            </p>
        </div>
    </div>
 
   <form id="form1" action="{{ \URL::to('/mails/send/'.$parentType.'/'.$parent.'/'.$offset) }}">
         <input type="hidden" name="parentType" value="{{ $parentType }}" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="{{ $parent }}" class="form-control" placeholder="">
         <input type="hidden" name="offset" value="{{ $offset }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>Addressed:</label>
                    <input type="text" name="addresed" value="{{ $addressed }}" 
                    	style="width:800px" class="form-control">
                    <br />"all": all members or "email1, email2,..."    
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>Subject:</label>
                    <input type="text" name="subject" value="{{ $subject }}" 
                    	style="width:800px" class="form-control">
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>MailBody:</label>
                    <textarea cols="80" rows="10"" name="mailbody" style="width:800px" class="form-control">{!! $mailbody !!}</textarea>
                    <br />
                    Mini markdown **bold**, http[s]:..., #...., ##...., {name},  image:![](url)
                </div>
            </div>
         </div>
         <div><strong><var id="stopper"></var></strong></div>    
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
         </div>
    </form>
    @if ($offset > 0) 
    <p><strong>
        This page will be sent automatically in {{ env('MAIL_WAIT_SEC',120) }} seconds.
        </strong>
    </p>
    @endif
    <script>
        var min = 0;
        var sec = 0;
        var counter = 0;
        var offset = {{ $offset }};
        var mailWaitSec = {{ env('MAIL_WAIT_SEC',120) }}; 
        $(function() {
            if (offset > 0) {
              $('#stopper').html(min+':'+sec);
              setTimeout('step()',1000);
            }  
        });
        function step() {
            counter++;
            if (sec < 59) {
                sec++;
            } else {
                sec = 0;
                min++;
            }
            $('#stopper').html(min+':'+sec);
            if ((counter >= mailWaitSec) & (offset > 0)) {
                 $('#form1').submit();   
            } else {
                setTimeout('step()',1000);
            }    
        }
    </script>    
</x-guest-layout>  
