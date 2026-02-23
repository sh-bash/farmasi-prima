<?php

namespace App\Livewire\Account\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name;
    public $roleId;
    public $search = '';
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteId;

    public $permissions = [];
    public $selectedPermissions = [];

    protected function rules()
    {
        return [
            'name' => 'required|unique:roles,name,' . $this->roleId,
        ];
    }

    public function mount()
    {
        $this->permissions = Permission::orderBy('name')->get();
    }

    public function render()
    {
        $roles = Role::where('name', 'like', '%' . $this->search . '%')
            ->with('permissions')
            ->latest()
            ->paginate(5);

        return view('livewire.account.role.index', compact('roles'))
            ->layout('layouts.app', [
                'title' => 'Master Role',
                'subtitle' => 'Manage role & permissions',
            ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $role = Role::updateOrCreate(
            ['id' => $this->roleId],
            ['name' => $this->name]
        );

        // Sync permission ke role
        $role->syncPermissions($this->selectedPermissions);

        $this->showForm = false;
        $this->resetForm();
        $this->resetPage();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Role saved successfully'
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        $this->isEdit = true;
        $this->showForm = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal-confirm',
            title: 'Delete Role?',
            text: 'This role will be deleted'
        );
    }

    public function delete()
    {
        Role::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'Role deleted successfully'
        );
    }

    public function resetForm()
    {
        $this->name = '';
        $this->roleId = null;
        $this->selectedPermissions = [];
        $this->isEdit = false;
        $this->showForm = false;
    }
}