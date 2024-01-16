<div>
    <x-header title="Colaboradores" separator/>

    <div class="flex items-center space-x-4 mb-4">
        <div class="w-1/3">
            <x-input
                label="Buscar por nome ou email"
                wire:model.live="search"
                icon="o-magnifying-glass"
            />
        </div>

        <div class="w-1/3">
            <x-choices
                label="Permissões"
                :options="$permissionsToSearch"
                wire:model.live="search_permissions"
                search-function="filterPermissions"
                option-label="name"
                searchable
                no-result-text="Nenhuma permissão encontrada"
            />
        </div>

        <x-select
            label="Resultados por página"
            wire:model.live="perPage"
            :options="[['id' => 5, 'name' => 5], ['id' => 15, 'name' => 15], ['id' => 25, 'name' => 25], ['id' => 50, 'name' => 50]]"
        />

        <x-checkbox
            label='Mostrar deletados'
            wire:model.live="search_trash"
            class="checkbox-primary"
            right
            tight
        />
    </div>

    <x-table :headers="$this->headers" :rows="$this->users">
        @scope('header_id', $header)
        <x-table-th :$header name="id"/>
        @endscope

        @scope('header_name', $header)
        <x-table-th :$header name="name"/>
        @endscope

        @scope('header_email', $header)
        <x-table-th :$header name="email"/>
        @endscope

        @scope('cell_permissions', $user)
        @foreach($user->permissions as $permission)
            <x-badge :value="$permission->name" class="badge-primary"/>
        @endforeach
        @endscope

        @scope('actions', $user)
        @unless($user->trashed())
            <x-button icon="o-trash" class="btn-xs" wire:click="delete({{ $user->id }})" spinner/>
        @else
            <x-button icon="o-arrow-path-rounded-square" class="btn-xs" wire:click="restore({{ $user->id }})" spinner/>
        @endunless
        @endscope
    </x-table>

    {{ $this->users->links()}}
</div>
