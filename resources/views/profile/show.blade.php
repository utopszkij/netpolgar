<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    
    <?php
    $accrediteds1 = \DB::table('likes')
    ->select('teams.id','teams.name as teamName','users.name as userName','users.profile_photo_path','users.email')
    ->join('members','members.id','likes.parent')
    ->join('teams','teams.id','members.parent')
    ->join('users','users.id','members.user_id')
    ->where('likes.parent_type','=','members')
    ->where('likes.user_id','=',\Auth::user()->id)
    ->where('members.rank','=','accredited')
    ->where('members.parent_type','=','teams');
    $accrediteds = \DB::table('likes')
    ->select('projects.id','projects.name as teamName','users.name as userName','users.profile_photo_path','users.email')
    ->join('members','members.id','likes.parent')
    ->join('projects','projects.id','members.parent')
    ->join('users','users.id','members.user_id')
    ->where('likes.parent_type','=','members')
    ->where('likes.user_id','=',\Auth::user()->id)
    ->where('members.rank','=','accredited')
    ->where('members.parent_type','=','projects')
    ->union($accrediteds1)
    ->get();
    
    // echo $accrediteds->toSql(); exit();
    
    foreach ($accrediteds as $accredited) {
        $accredited->avatar = \App\Models\Avatar::userAvatar($accredited->profile_photo_path, $accredited->email);
    }
    ?>
    
    @if (count($accrediteds) > 0)
    <div class="row accrediteds">
    	<div class="col-12">
        	<h3>Szavazásra meghatalmazott képviselők</h3>
        	<table>
        	<tbody>
        	@foreach ($accrediteds as $key => $item)
        	<tr><td>{{ $item->teamName}}
        	    </td><td><img class="avatar" src="{{ $item->avatar }}" />{{ $item->userName}}</td>
        	</tr>
        	@endforeach
        	</tbody>
        	</table>
    	</div>
    </div>
    @endif

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-jet-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-jet-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
