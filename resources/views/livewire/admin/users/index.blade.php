<div>
    <x-header title="Users" separator/>

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
