<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your account information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div>
                <flux:input
                    wire:model="email"
                    disabled
                    readonly
                    copyable
                    variant="filled"
                    :label="__('Email')"
                    type="email"
                    autocomplete="email"
                />

                @if (! user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link
                                class="cursor-pointer text-sm"
                                wire:click.prevent="resendVerificationNotification"
                            >
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="!dark:text-green-400 mt-2 font-medium !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif

                        @if (session('error') === 'verification-link-failed')
                            <flux:text class="!dark:text-red-400 mt-2 font-medium !text-red-600">
                                {{ __('Failed to send verification link. Please try again.') }}
                            </flux:text>
                        @endif

                        @if (session('custom_status'))
                            <flux:text class="!dark:text-green-400 mt-2 font-medium !text-green-600">
                                {{ session('custom_status') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:input
                wire:model="first_name"
                :label="__('First Name')"
                type="text"
                required
                autofocus
                autocomplete="first_name"
            />
            <flux:input
                wire:model="last_name"
                :label="__('Last Name')"
                type="text"
                autofocus
                autocomplete="last_name"
            />
            <flux:select wire:model="timezone" :label="__('Timezone')" :placeholder="__('Select your timezone')">
                @foreach ($timezones as $timezone => $name)
                    <flux:select.option :value="$timezone">{{ $name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
