<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Resend Verification Email') }}
                    </button>&nbsp;
	                <a class="btn btn-secondary" href="{{ url('/') }}">
    	                <em class="fas fa-undo-alt"></em>&nbsp;{{ __('backHome') }}
        	        </a>
                    
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn btn-primary">
                    {{ __('Log Out') }}
                </button>&nbsp;
                <a class="btn btn-secondary" href="{{ url('/') }}">
                    <em class="fas fa-undo-alt"></em>&nbsp;{{ __('backHome') }}
                </a>

            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>