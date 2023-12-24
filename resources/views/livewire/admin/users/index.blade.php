<div>
    <x-header title="Users" separator/>

    <div class="flex justify-between mb-4">
        <div class="w-1/3">
            <x-input wire:model.live="search" icon="o-magnifying-glass" class="input-sm"
                     placeholder="Search by name or email..."/>
        </div>
        <x-button icon="o-plus" wire:click="create" spinner/>
    </div>

    <x-table :headers="$this->headers" :rows="$this->users">
        @scope('cell_permissions', $user)
        @foreach($user->permissions as $permission)
            <x-badge :value="$permission->name" class="badge-primary"/>
        @endforeach
        @endscope

        @scope('actions', $user)
        <x-button icon="o-trash" class="btn-xs" wire:click="delete({{ $user->id }})" spinner/>
        @endscope
    </x-table>
</div>
