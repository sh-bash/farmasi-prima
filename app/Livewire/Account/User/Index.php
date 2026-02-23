<?php

namespace App\Livewire\Account\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name, $email, $password, $role;
    public $userId;

    public $search = '';
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteId;

    public $roles = [];

    protected function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId)
            ],
            'role' => 'required',
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
    }

    public function render()
    {
        $users = User::with('roles')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(5);

        return view('livewire.account.user.index', compact('users'))
            ->layout('layouts.app', [
                'title' => 'Master User',
                'subtitle' => 'Manage user data',
            ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
                    ? Hash::make($this->password)
                    : User::find($this->userId)?->password
            ]
        );

        // Assign role (permission ikut role)
        $user->syncRoles([$this->role]);

        $this->showForm = false;
        $this->resetForm();
        $this->resetPage();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'User saved successfully'
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name;

        $this->isEdit = true;
        $this->showForm = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal-confirm',
            title: 'Delete User?',
            text: 'This user will be deleted'
        );
    }

    public function delete()
    {
        User::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'User deleted successfully'
        );
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->userId = null;
        $this->isEdit = false;
        $this->showForm = false;
    }
}