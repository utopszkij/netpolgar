<x-guest-layout>
    <x-jet-authentication-card>

        <x-jet-validation-errors class="mb-4" />

        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>
            <div class="flex items-center justify-end mt-4">
            	<a class="underline text-sm text-gray-600 hover:text-gray-900" 
            	   href="{{ url('/register') }}">
            	   {{ __('Not accounts? - register') }}
            	</a>
			</div>
            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
			</div>
            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <em class="fa fa-check"></em>&nbsp;{{ __('Login') }}
                </button>&nbsp;    
                <a class="btn btn-secondary" href="{{ url('/') }}">
                    <em class="fas fa-undo-alt"></em>&nbsp;{{ __('backHome') }}
                </a>
            </div>
            

            {{-- Login with Facebook --}}
            <div class="flex items-center justify-end mt-4">
                <a class="btn" href="{{ url('auth/facebook') }}"
                    style="background: #3B5499; color: #ffffff; padding: 10px; width: 100%; text-align: center; display: block; border-radius:3px;">
                    <em class="fab fa-facebook"></em>&nbsp;{{ __('Login with Facebook') }}
                </a>
            </div>

            {{-- Login with Google --}}
            <div class="flex items-center justify-end mt-4">
                <a class="btn" href="{{ url('auth/google') }}"
                    style="background: silver; color: black; padding: 10px; width: 100%; text-align: center; display: block; border-radius:3px;">
                    <em class="fab fa-google"></em>&nbsp;{{ __('Login with Google') }}
                </a>
            </div>

            {{-- Login with Github --}}
            <div class="flex items-center justify-end mt-4">
                <a class="btn" href="{{ url('auth/github') }}"
                    style="background: silver; color: black; padding: 10px; width: 100%; text-align: center; display: block; border-radius:3px;">
                    <em class="fab fa-github"></em>&nbsp;{{ __('Login with Github') }}
                </a>
            </div>
            
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
