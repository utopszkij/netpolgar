<html>
<head>
    @include('layouts/htmlhead')
</head>    
<body>	
   <div id="help" class="row help">
        <div class="col-12" style="text-align:right">
            <a href="#" onclick="desktopClick()" title="desktop, laptop">
                <em class="fas fa-desktop"></em>
            </a>
            &nbsp;
            <a href="#" onclick="phoneClick()" title="okostelefon">
                <em class="fas fa-mobile-alt"></em>
            </a>
            &nbsp;
            &nbsp;
            &nbsp;
            <button type="button" class="btn btn-secondary" title="close"
                onclick="window.close()">X
            </button>&nbsp;
        </div>
        <div class="col-12">
            <iframe id="ifrm"></iframe>
        </div>
    </div>    
    <script>
        var name = "{{ $name }}";
        var url = '';
        var urls = {
            d_beszelgetes : "https://docs.google.com/presentation/d/e/2PACX-1vT0aecyJmHb4oPIY6_u6u8eORZPV97rw9XoXWUmZN6FJhTJfYrc9QkWG8Pgg6IZa-gwotZTCeWA1iyM/embed?start=false&loop=false&delayms=3000",
            p_beszelgetes : "https://docs.google.com/presentation/d/e/2PACX-1vTR9nVg6KuFdFw94Rn9q9sOAQYcWSncF5qZ1svqbIfFmJz521Z8Cq7e9sr1ecIpGW5W4TvzqAyUZRQG/embed?start=false&loop=false&delayms=3000" 
        };
        $('#ifrm').attr('height',(window.innerHeight - 100));
        $('#ifrm').attr('width',(window.innerWidth - 40));
        if (window.innerWidth < 575) {
            phoneClick();
        } else {
            desktopClick();
        }
        function phoneClick() {
            url = urls['p_'+name];
            if (url != undefined) {
                $('#ifrm').attr('src',url);
            }
            return false;
        }
        function desktopClick() {
            url = urls['d_'+name];
            if (url != undefined) {
                $('#ifrm').attr('src',url);
            }
            return false;
        }
    </script>
</body>
</html>
