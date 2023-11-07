<x-card title="Login" class="mx-auto w-[420px]" shadow>
    @if($message = session()->get('status token'))
        <x-alert icon="o-exclamation-triangle" class="alert-error text-xs mb-4">
            {{$message}}
        </x-alert>
    @endif

    @if($errors->hasAny(['invalidCredentials', 'rateLimit']))
        <x-alert icon="o-exclamation-triangle" class="alert-warning text-xs mb-4">
            @error('invalidCredentials')
            <span>{{ $message }}</span>
            @enderror

            @error('rateLimit')
            <span>{{ $message }}</span>
            @enderror
        </x-alert>
    @endif

    <x-form wire:submit="login">
        <x-input label="Email" wire:model="email"/>
        <x-input label="Password" wire:model="password" type="password"/>
        <a wire:navigate href="{{route('password.recovery')}}"
           class="btn-ghost p-2 rounded text-xs underline ml-auto">
            Forgot Password?
        </a>

        <x-slot:actions>
            <div class="flex items-center justify-between w-full">
                <a wire:navigate href="{{route('register')}}"
                   class="btn-ghost p-2 rounded text-xs underline mr-auto">
                    I want to create an account
                </a>

                <div class="flex items-center space-x-4">
                    <x-button label="Login" class="btn-primary" type="submit" spinner="submit"/>
                </div>
            </div>
        </x-slot:actions>
    </x-form>
</x-card>
