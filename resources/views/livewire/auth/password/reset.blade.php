<x-card title="Password Reset" class="mx-auto w-[420px]" shadow>
    @if($message = session()->get('status token'))
        <x-alert icon="o-exclamation-triangle" class="alert-error text-xs mb-4">
            {{$message}}
        </x-alert>
    @endif

    <x-form wire:submit="updatePassword">
        <x-input label="Email" value="{{$this->obfuscatedEmail}}" readonly/>
        <x-input label="Email Confirmation" wire:model="email_confirmation"/>
        <x-input label="Password" wire:model="password" type="password"/>
        <x-input label="Password Confirmation" wire:model="password_confirmation" type="password"/>

        <x-slot:actions>
            <div class="flex items-center justify-between w-full">
                <a wire:navigate href="{{route('login')}}"
                   class="btn-ghost p-2 rounded text-xs underline mr-auto">
                    Never mind, get back to login
                </a>

                <div class="flex items-center space-x-4">
                    <x-button label="Reset" class="btn-primary" type="submit" spinner="submit"/>
                </div>
            </div>
        </x-slot:actions>
    </x-form>
</x-card>
