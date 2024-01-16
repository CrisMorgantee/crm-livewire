<?php

namespace App\Livewire\Admin\Users;

use App\Enum\Can;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read LengthAwarePaginator|User[] $users
 * @property-read array $headers
 */
class Index extends Component
{
    /**
     * @var string|null
     */
    public ?string $search = null;

    public array $search_permissions = [];

    public Collection $permissionsToSearch;

    public bool $search_trash = false;

    public string $sortDirection = 'asc';

    public string $sortColumnBy = 'name';

    public function mount(): void
    {
        $this->authorize(Can::BE_AN_ADMIN->value);
        $this->filterPermissions();
    }

    #[Computed]
    public function filterPermissions(?string $value = ''): void
    {
        $this->permissionsToSearch = Permission::query()
            ->when($value, fn(Builder $q) => $q->where('name', 'like', '%' . $value . '%'))
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.admin.users.index');
    }

    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->when(
                $this->search,
                fn(Builder $q) => $q
                    ->where(
                        DB::raw('lower(name)'),
                        'like',
                        '%' . strtolower($this->search) . '%'
                    )
                    ->orWhere(
                        'email',
                        'like',
                        '%' . strtolower($this->search) . '%'
                    )
            )
            ->when(
                $this->search_permissions,
                fn(Builder $q) => $q->whereHas(
                    'permissions',
                    fn(Builder $query) => $query->whereIn('permissions.id', $this->search_permissions)
                )
            )
            ->when(
                $this->search_trash,
                fn(Builder $q) => $q->onlyTrashed() // @phpstan-ignore-line
            )
            ->orderBy($this->sortColumnBy, $this->sortDirection)
            ->get();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'sortColumnBy' => $this->sortColumnBy, 'sortDirection' => $this->sortDirection, 'class' => 'text-gray-200'],
            ['key' => 'name', 'label' => 'Nome', 'sortColumnBy' => $this->sortColumnBy, 'sortDirection' => $this->sortDirection, 'class' => 'text-gray-200'],
            ['key' => 'email', 'label' => 'Email', 'sortColumnBy' => $this->sortColumnBy, 'sortDirection' => $this->sortDirection, 'class' => 'text-gray-200'],
            ['key' => 'permissions', 'label' => 'Permissões', 'sortColumnBy' => $this->sortColumnBy, 'sortDirection' => $this->sortDirection, 'class' => 'text-gray-200'],
        ];
    }

    public function sortBy(string $column, string $direction): void
    {
        $this->sortColumnBy  = $column;
        $this->sortDirection = $direction;
    }
}
