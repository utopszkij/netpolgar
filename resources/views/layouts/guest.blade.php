<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	@include('layouts/htmlhead')
    </head>
    <body>
    	<div>
	    	@include('navigation-menu')
    	</div>
    	<main>
	        <div class="font-sans text-gray-900 antialiased">
	            {{ $slot }}
	        </div>
        </main>
        @include('footer')
    </body>
</html>
