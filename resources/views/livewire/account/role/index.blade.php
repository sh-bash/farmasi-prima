<div>

    {{-- Button --}}
    <button class="btn btn-primary mb-2" wire:click="create">
        + Add Role
    </button>

    {{-- FORM --}}
    @if($showForm)
    <div class="card mb-3">
        <div class="card-header">
            {{ $isEdit ? 'Edit Role' : 'Create Role' }}
        </div>

        <div class="card-body">

            <div class="mb-2">
                <label>Role Name</label>
                <input type="text" class="form-control" wire:model="name">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <label>Permissions</label>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-3">
                        <label>
                            <input type="checkbox"
                                value="{{ $permission->name }}"
                                wire:model="selectedPermissions">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-success mt-3" wire:click="save">Save</button>
            <button class="btn btn-secondary mt-3" wire:click="resetForm">Cancel</button>
        </div>
    </div>
    @endif


    {{-- TABLE --}}
    @if($showTable)
    <div class="card">
        <div class="card-header">
            Role List
        </div>

        <div class="card-body">

            <input type="text"
                   class="form-control mb-2"
                   placeholder="Search role..."
                   wire:model.debounce.500ms="search">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="120">Action</th>
                        <th>Role</th>
                        <th>Permissions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-alt-warning"
                                        wire:click="edit({{ $role->id }})"
                                        title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-alt-danger"
                                        wire:click="confirmDelete({{ $role->id }})"
                                        title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td>{{ $role->name }}</td>
                        <td>
                            {{ $role->permissions->pluck('name')->join(', ') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $roles->links() }}
        </div>
    </div>
    @endif

</div>