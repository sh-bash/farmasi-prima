{{-- resources/views/livewire/master/product/index.blade.php --}}

<div class="container mt-4">

    {{-- Form --}}
    {{-- Button Add --}}
    <div class="mb-3">
        <button class="btn btn-primary"
                wire:click="create">
            + Add Product
        </button>
    </div>

    {{-- Form Block --}}
    @if($showForm)
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                {{ $isEdit ? 'Edit Product' : 'Add Product' }}
            </h3>

            <div class="block-options">
                <button type="button"
                        class="btn-block-option"
                        wire:click="toggleForm">
                    <i class="si si-close"></i>
                </button>
            </div>
        </div>

        <div class="block-content">
            <form wire:submit.prevent="save">

                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control" wire:model="barcode">
                    @error('barcode') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" wire:model="name">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" wire:model="category_id">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Form (Sediaan)</label>
                        <select class="form-select" wire:model="form_id">
                            <option value="">-- Select Form --</option>
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }}</option>
                            @endforeach
                        </select>
                        @error('form_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">HET</label>
                    <input type="number" class="form-control" wire:model="het">
                    @error('het') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Ingredients Detail --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ingredients (Zat Aktif)</label>
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Ingredient</th>
                                <th width="130">Strength</th>
                                <th width="110">Unit</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingredientRows as $idx => $row)
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm"
                                                wire:model="ingredientRows.{{ $idx }}.ingredient_id">
                                            <option value="">-- Select --</option>
                                            @foreach($ingredients as $ing)
                                                <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("ingredientRows.$idx.ingredient_id")
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number"
                                               class="form-control form-control-sm"
                                               wire:model="ingredientRows.{{ $idx }}.strength"
                                               placeholder="e.g. 500">
                                        @error("ingredientRows.$idx.strength")
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               wire:model="ingredientRows.{{ $idx }}.unit"
                                               placeholder="mg / ml">
                                        @error("ingredientRows.$idx.unit")
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-alt-danger"
                                                wire:click="removeIngredientRow({{ $idx }})">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted small">
                                        No ingredients added.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button"
                            class="btn btn-sm btn-alt-secondary"
                            wire:click="addIngredientRow">
                        <i class="fa fa-plus me-1"></i> Add Ingredient
                    </button>
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

    {{-- Search --}}
    <div class="mb-2">
        <input type="text"
               class="form-control"
               placeholder="Search product..."
               wire:model.live="search">
    </div>

    {{-- Table --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Product List</h3>

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
                        <th>Name</th>
                        <th>Form</th>
                        <th>Category</th>
                        <th width="150">HET</th>
                        <th width="160">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
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
                            <td>
                                {{ $p->name }}
                            @if($p->ingredients->count())
                                <small class="text-muted d-block">
                                    @foreach($p->ingredients as $i)
                                        <div>
                                            {{ $i->name }}
                                            @if($i->pivot->strength)
                                                {{ number_format($i->pivot->strength, 0) }} {{ $i->pivot->unit }}
                                            @endif
                                        </div>
                                    @endforeach
                                </small>
                            @endif
                            </td>
                            <td>{{ $p->form->name }}</td>
                            <td>{{ $p->category->name }}</td>
                            <td>{{ number_format($p->het) }}</td>
                            <td>
                                {{ $p->created_at->format('d M Y') }}<br>
                                <small class="text-muted">
                                    {{ $p->created_at->format('H:i') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $products->links() }}
            </div>
        </div>
    </div>

</div>
