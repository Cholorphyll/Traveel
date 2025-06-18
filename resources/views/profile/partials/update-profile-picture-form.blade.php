<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Image') }}
        </h2>

        <img class="w-8 h-8 rounded-full" src="{{"storage/app/public/$user->profilepic"}}" alt="user profile picture" />

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your profile picture.") }}
        </p>
    </header>
    
    @if (Session('message'))
        <div class="text-red-500">
            {{ session('message') }}
        </div>
    @endif

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.profilepic') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profilepic" :value="__('Profile Picture')" />
            <x-text-input id="profilepic" name="profilepic" type="file" class="mt-1 block w-full form-control mb-3" :value="old('profilepic', $user->profilepic)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('profilepic')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
