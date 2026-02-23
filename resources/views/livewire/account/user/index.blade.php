<div>

    <button class="btn btn-primary mb-2" wire:click="create">
        + Add User
    </button>

    {{-- FORM --}}
    @if($showForm)
    <div class="card mb-3">
        <div class="card-header">
            {{ $isEdit ? 'Edit User' : 'Create User' }}
        </div>

        <div class="card-body">

            <div class="mb-2">
                <label>Name</label>
                <input type="text" class="form-control" wire:model="name">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-2">
                <label>Email</label>
                <input type="email" class="form-control" wire:model="email">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-2">
                <label>Password {{ $isEdit ? '(optional)' : '' }}</label>
                <input type="password" class="form-control" wire:model="password">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-2">
                <label>Role</label>
                <select class="form-control" wire:model="role">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
                @error('role') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button class="btn btn-success mt-2" wire:click="save">Save</button>
            <button class="btn btn-secondary mt-2" wire:click="resetForm">Cancel</button>
        </div>
    </div>
    @endif


    {{-- TABLE --}}
    @if($showTable)
    <div class="card">
        <div class="card-header">
            User List
        </div>

        <div class="card-body">

            <input type="text"
                   class="form-control mb-2"
                   placeholder="Search name or email..."
                   wire:model.debounce.500ms="search">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="120">Action</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-alt-warning"
                                        wire:click="edit({{ $user->id }})"
                                        title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-alt-danger"
                                        wire:click="confirmDelete({{ $user->id }})"
                                        title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>
    @endif

</div>