<html>
<head>
    @include('layouts/htmlhead')
</head>    
<body>	
   <div id="help" class="row help">
        <div class="col-3" style="text-align:left; padding:2px">
            <a href="{{ \URL::to('/help/page/home?m='.$m) }}"><em class="fas fa-home"></em>Kezdőlaop</a>
        </div>
        <div class="col-9" style="text-align:right; padding:2px">
            @if (file_exists('../resources/views/help/'.$name.'_p.blade.php')) 
                <a href="/help/page/{{ $name }}&m=d" 
                    onclick="true;" title="desktop, laptop">
                    <em class="fas fa-desktop"></em>
                </a>
                &nbsp;
                <a href="/help/page/{{ $name }}&m=p" o
                    nclick="true" title="okostelefon">
                    <em class="fas fa-mobile-alt"></em>
                </a>
                &nbsp;
                &nbsp;
                &nbsp;
            @endif
            <button type="button" onclick="window.close()" class="btn btn-secondary">
                X
            </button>    
        </div>
        <div class="col-12" style="text-align:left; padding:10px; min-height:300px">
                @if (file_exists('../resources/views/help/'.$name.'_'.$m.'.blade.php')) 
                    @include('help.'.$name.'_'.$m)
                @else @if (file_exists('../resources/views/help/'.$name.'.blade.php')) 
                        @include('help.'.$name)
                      @else 
                        <div style="text-align:center">
                            <img src="{{ \URL::to('/') }}/img/construction.png" /> 
                            <br />Készül  ({{ $name }}) 
                        </div>
                      @endif  
                @endif    
        </div>
   </div>    
</body>
</html>
