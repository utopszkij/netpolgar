<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	@include('layouts/htmlhead')
    </head>
    <body>
    	<div>
	    	@include('navigation-menu')
    	</div>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
        @include('footer')
    </body>
</html>
