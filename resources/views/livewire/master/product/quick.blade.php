

    <div class="modal fade" id="quickProductModal" tabindex="-1" wire:ignore.self>
        <style>
            /* Samakan tinggi select2 dengan bootstrap */
            .select2-container--default .select2-selection--single {
                height: 38px !important;
                padding: 6px 12px !important;
                border: 1px solid #ced4da !important;
                border-radius: 0.375rem !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 24px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }
        </style>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Quick Create Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- BASIC INFO --}}
                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <label>Product Name</label>
                            <input type="text" class="form-control" wire:model="name">
                        </div>

                        <div class="col-md-6">
                            <label>Barcode</label>
                            <input type="text" class="form-control" wire:model="barcode">
                        </div>

                        <div class="col-md-4">
                            <label>HET</label>
                            <input type="number" class="form-control" wire:model="het">
                        </div>

                        {{-- Category --}}
                        <div class="col-md-4">
                            <label>Category</label>
                            <div wire:ignore>
                                <select class="form-control select2-category">
                                    <option value="">-- Select --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Form --}}
                        <div class="col-md-4">
                            <label>Form</label>
                            <div wire:ignore>
                                <select class="form-control select2-form">
                                    <option value="">-- Select --</option>
                                    @foreach($forms as $form)
                                        <option value="{{ $form->id }}">
                                            {{ $form->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <hr>
                    <h6>Kandungan Produk</h6>

                    @foreach($ingredients as $index => $row)
                    <div class="row g-2 mb-2 align-items-end"
                         wire:key="ingredient-row-{{ $index }}">

                        <div class="col-md-5">
                            <label>Ingredient</label>
                            <div wire:ignore>
                                <select class="form-control select2-ingredient"
                                        data-index="{{ $index }}">
                                    <option value="">-- Select --</option>
                                    @foreach($ingredientList as $ing)
                                        <option value="{{ $ing->id }}">
                                            {{ $ing->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label>Strength</label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="ingredients.{{ $index }}.strength">
                        </div>

                        <div class="col-md-2">
                            <label>Unit</label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="ingredients.{{ $index }}.unit">
                        </div>

                        <div class="col-md-2">
                            <button type="button"
                                    class="btn btn-danger w-100"
                                    wire:click="removeIngredientRow({{ $index }})">
                                Remove
                            </button>
                        </div>

                    </div>
                    @endforeach

                    <button type="button"
                            class="btn btn-sm btn-primary mt-2"
                            wire:click="addIngredientRow">
                        + Add Ingredient
                    </button>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button class="btn btn-success" wire:click="save" type="button">
                        Save Product
                    </button>
                </div>

            </div>
        </div>
    </div>

