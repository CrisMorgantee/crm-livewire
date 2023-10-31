<x-card title="Register" shadow="true" class="mx-auto w-[420px]">
    <x-form wire:submit="submit">
        <x-input label="Name" wire:model="name"/>
        <x-input label="Email" wire:model="email"/>
        <x-input label="Confirm Your Email" wire:model="email_confirmation"/>
        <x-input label="Password" wire:model="password" type="password"/>
        <x-slot:actions>
            <div class="flex items-center justify-between w-full">
                <a wire:navigate href="{{route('login')}}"
                   class="btn-ghost p-2 rounded text-xs underline mr-auto">
                    I already have an account
                </a>

                <div class="flex items-center space-x-4">
                    <x-button label="Reset" type="reset"/>
                    <x-button label="Register!" class="btn-primary" type="submit" spinner="submit"/>
                </div>
            </div>

        </x-slot:actions>
    </x-form>
</x-card>
