<x-card title="Password Recovery" class="mx-auto w-[420px]" shadow>
    @if ($message)
        <x-alert icon="o-exclamation-triangle" class="alert-success text-xs mb-4">
            {{ $message }}
        </x-alert>
    @endif

    <x-form wire:submit="requestPasswordRecovery">
        <x-input label="Email" wire:model="email"/>

        <x-slot:actions>
            <div class="flex items-center justify-between w-full">
                <a wire:navigate href="{{route('login')}}"
                   class="btn-ghost p-2 rounded text-xs underline mr-auto">
                    Never mind, get back to login
                </a>

                <div class="flex items-center space-x-4">
                    <x-button label="Submit" class="btn-primary" type="submit" spinner="submit"/>
                </div>
            </div>
        </x-slot:actions>
    </x-form>
</x-card>
