{{-- resources/views/livewire/master/supplier/index.blade.php --}}

<div class="container mt-4">

    {{-- Form --}}
    {{-- Button Add --}}
    <div class="mb-3">
        <button class="btn btn-primary"
                wire:click="create">
            + Add Supplier
        </button>
    </div>

    {{-- Form Block --}}
    @if($showForm)
    <div class="block block-rounded mb-4">
        <div class="block-header">
            <h3 class="block-title">
                {{ $isEdit ? 'Edit Supplier' : 'Add Supplier' }}
            </h3>

            <div class="block-options">
                <button class="btn-block-option" wire:click="$set('showForm', false)">
                    <i class="si si-close"></i>
                </button>
            </div>
        </div>

        <div class="block-content">
            <form wire:submit.prevent="save">

                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Code</label>
                        <input class="form-control" wire:model="code">
                        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input class="form-control" wire:model="name">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Location</label>
                        <input class="form-control" wire:model="location">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>PIC</label>
                        <input class="form-control" wire:model="person_in_charge">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Contact</label>
                        <input class="form-control" wire:model="contact">
                    </div>

                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button class="btn btn-success w-100">
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="mb-2">
        <input type="text"
               class="form-control"
               placeholder="Search supplier..."
               wire:model.live="search">
    </div>

    {{-- Table --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Supplier List</h3>

            <div class="block-options">
                {{-- Export --}}
                <button type="button"
                    class="btn-block-option"
                    wire:click="export"
                    title="Export Excel">
                <i class="fa fa-file-excel text-success"></i>
                </button>

                {{-- Download Template --}}
                <button type="button"
                    class="btn-block-option"
                    wire:click="downloadTemplate"
                    title="Download Template">
                    <i class="fa fa-download text-info"></i>
                </button>

                {{-- Import --}}
                <label class="btn-block-option mb-0" title="Import Excel" style="cursor:pointer;">
                    <i class="fa fa-upload text-primary"></i>
                    <input type="file"
                        wire:model="importFile"
                        accept=".xlsx,.xls"
                        style="display:none;">
                </label>

                {{-- Refresh --}}
                <button type="button" class="btn-block-option" wire:click.stop.prevent="refreshData" title="Refresh">
                    <i class="si si-refresh"></i>
                </button>

                {{-- Hide / Show --}}
                <button type="button" class="btn-block-option" wire:click.stop.prevent="toggleTable" title="Hide / Show">
                    <i class="si {{ $showTable ? 'si-arrow-up' : 'si-arrow-down' }}"></i>
                </button>
            </div>
        </div>

        <div class="block-content" @if(!$showTable) style="display:none;" @endif>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="120">Action</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>PIC</th>
                        <th>Telp</th>
                        <th width="160">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $p)
                        <tr>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-alt-warning"
                                            wire:click="edit({{ $p->id }})"
                                            title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-alt-danger"
                                            wire:click="confirmDelete({{ $p->id }})"
                                            title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                            <td>{{ $p->code }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->location }}</td>
                            <td>{{ $p->person_in_charge }}</td>
                            <td>{{ $p->contact }}</td>
                            <td>
                                {{ $p->created_at->format('d M Y') }}<br>
                                <small class="text-muted">
                                    {{ $p->created_at->format('H:i') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

</div>