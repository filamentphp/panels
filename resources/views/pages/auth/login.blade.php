<div>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/login.buttons.register.before') }}

            <x-filament::link :href="filament()->getRegistrationUrl()">
                {{ __('filament::pages/auth/login.buttons.register.label') }}
            </x-filament::link>
        </x-slot>
    @endif

    <form
        wire:submit.prevent="authenticate"
        class="grid gap-y-8"
    >
        {{ $this->form }}

        {{ $this->authenticateAction }}
    </form>
</div>
