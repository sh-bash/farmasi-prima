<div class="container mt-4">

    <div class="mb-3">
        <button class="btn btn-primary" wire:click="create">
            + Add Patient
        </button>
    </div>

    @if($showForm)
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                {{ $isEdit ? 'Edit Patient' : 'Add Patient' }}
            </h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" wire:click="toggleForm">
                    <i class="si si-close"></i>
                </button>
            </div>
        </div>

        <div class="block-content">
            <form wire:submit.prevent="save">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Patient Code</label>
                        <input type="text" class="form-control" wire:model="medical_record_number">
                        @error('medical_record_number') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-control" wire:model="gender">
                            <option value="">-- Select --</option>
                            <option value="L">Male</option>
                            <option value="P">Female</option>
                        </select>
                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Birth Date</label>
                        <input type="date" class="form-control" wire:model="birth_date">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" wire:model="phone">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" wire:model="address"></textarea>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-success">
                        {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif

    <div class="mb-2">
        <input type="text"
               class="form-control"
               placeholder="Search patient..."
               wire:model.live="search">
    </div>

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Patient List</h3>
        </div>

        <div class="block-content" @if(!$showTable) style="display:none;" @endif>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="120">Action</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Birth Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $p)
                        <tr>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-alt-warning"
                                            wire:click="edit({{ $p->id }})">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-alt-danger"
                                            wire:click="confirmDelete({{ $p->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                            <td>{{ $p->medical_record_number }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ ($p->gender == 'L') ? 'Male' : 'Female' }}</td>
                            <td>{{ $p->phone }}</td>
                            <td>{{ date('d F Y', strtotime($p->birth_date)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

</div>