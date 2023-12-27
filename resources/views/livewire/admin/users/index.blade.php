<div>
    <x-header title="Users" separator/>

    <div class="flex space-x-4 mb-4">
        <div class="w-1/3">
            <x-input
                label="Filter by name or email"
                placeholder="Filter by name or email..."
                wire:model.live="search"
                icon="o-magnifying-glass"
            />
        </div>

        <div class="w-1/3">
            <x-choices
                label="Permissions"
                :options="$permissionsToSearch"
                wire:model.live="search_permissions"
                search-function="filterPermissions"
                option-label="name"
                searchable
                no-result-text="No permissions found."

            />
        </div>
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
