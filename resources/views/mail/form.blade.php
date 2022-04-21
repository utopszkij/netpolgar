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
 
   <form action="{{ \URL::to('/mails/send/'.$parentType.'/'.$parent.'/'.$offset) }}">
         <input type="hidden" name="parentType" value="{{ $parentType }}" class="form-control" placeholder="">
         <input type="hidden" name="parent" value="{{ $parent }}" class="form-control" placeholder="">
         <input type="hidden" name="offset" value="{{ $offset }}" class="form-control" placeholder="">
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <label>Addressed:</label>
                    <input type="text" name="addresed" value="all" 
                    	style="width:800px" class="form-control">
                    <br />"all": all mebbers or "email1, email2,..."    
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
                    <label>MailBody (html):</label>
                    <textarea cols="80" rows="10"" name="mailbody" style="width:800px" class="form-control">{{ $mailbody}}</textarea>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
         </div>
    </form>
</x-guest-layout>  
