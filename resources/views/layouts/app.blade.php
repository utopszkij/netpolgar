<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	@include('layouts/htmlhead')
    </head>
    <body class="font-sans antialiased">
    <script>console.log('body után'); alert(1);</script>
        <x-jet-banner />
        <div class="min-h-screen bg-gray-100" id="appBlade">
            @livewire('navigation-menu')
            <!-- Page Content -->
            <main>
				@include('message')      
	            <!-- Page Heading -->
	            @if (isset($header))
	                <header class="bg-white shadow">
	                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
	                        {{ $header }}
	                    </div>
	                </header>
	            @endif
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        @include('footer')
    </body>
</html>
