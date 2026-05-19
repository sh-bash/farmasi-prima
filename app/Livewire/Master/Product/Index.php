<?php

namespace App\Livewire\Master\Product;

use App\Exports\Master\ProductsExport;
use App\Exports\Master\ProductsTemplateExport;
use App\Imports\Master\ProductsImport;
use App\Models\Master\Category;
use App\Models\Master\Form;
use App\Models\Master\Ingredient;
use App\Models\Master\Product;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $barcode, $name, $het;
    public $category_id, $form_id;
    public $ingredientRows = [];
    public $productId;
    public $search = '';
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteId;
    public $importFile;

    protected function rules()
    {
        return [
            'barcode' => [
            'required',
            Rule::unique('products', 'barcode')
                    ->whereNull('deleted_at')
                    ->ignore($this->productId)
            ],
            'name'        => 'required',
            'het'         => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'form_id'     => 'required|exists:forms,id',
            'ingredientRows.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'ingredientRows.*.strength'      => 'nullable|numeric',
            'ingredientRows.*.unit'          => 'nullable|string|max:50',
        ];
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function toggleTable()
    {
        $this->showTable = !$this->showTable;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('barcode', 'like', '%' . $this->search . '%')
            ->latest()
            ->with(['ingredients', 'category', 'form'])
            ->paginate(5);

        $categories  = Category::orderBy('name')->get();
        $forms       = Form::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();

        return view('livewire.master.product.index', compact('products', 'categories', 'forms', 'ingredients'))
                ->layout('layouts.app', [
                    'title' => 'Master Product',
                    'subtitle' => 'Manage product data',
                ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function refreshData()
    {
        // reset pagination agar reload dari page 1 (optional)
        $this->resetPage();
    }

    public function addIngredientRow()
    {
        $this->ingredientRows[] = ['ingredient_id' => '', 'strength' => '', 'unit' => ''];
    }

    public function removeIngredientRow($index)
    {
        array_splice($this->ingredientRows, $index, 1);
    }

    public function save()
    {
        $this->validate();

        $product = Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'barcode'     => $this->barcode,
                'name'        => $this->name,
                'het'         => $this->het,
                'category_id' => $this->category_id,
                'form_id'     => $this->form_id,
            ]
        );

        // Sync ingredients
        $sync = [];
        foreach ($this->ingredientRows as $row) {
            if (!empty($row['ingredient_id'])) {
                $sync[$row['ingredient_id']] = [
                    'strength' => $row['strength'] ?? null,
                    'unit'     => $row['unit'] ?? null,
                ];
            }
        }
        $product->ingredients()->sync($sync);

        $this->showForm = false;
        $this->resetForm();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Data saved successfully'
        ]);
    }

    public function edit($id)
    {
        $product = Product::with('ingredients')->findOrFail($id);

        $this->productId   = $product->id;
        $this->barcode     = $product->barcode;
        $this->name        = $product->name;
        $this->het         = $product->het;
        $this->category_id = $product->category_id;
        $this->form_id     = $product->form_id;

        $this->ingredientRows = $product->ingredients->map(fn($i) => [
            'ingredient_id' => $i->id,
            'strength'      => $i->pivot->strength,
            'unit'          => $i->pivot->unit,
        ])->toArray();

        $this->isEdit   = true;
        $this->showForm = true;
    }

    public function delete()
    {
        Product::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'Data deleted'
        );
    }

    public function resetForm()
    {
        $this->barcode        = '';
        $this->name           = '';
        $this->het            = '';
        $this->category_id    = '';
        $this->form_id        = '';
        $this->ingredientRows = [];
        $this->productId      = null;
        $this->isEdit         = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal-confirm',
            title: 'Delete Data?',
            text: 'This data will be moved to trash'
        );
    }

    public function export()
    {
        $today = date('Y-m-d H:i:s');
        return Excel::download(new ProductsExport, 'Products '.$today.'.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new ProductsTemplateExport,
            'Product Template.xlsx'
        );
    }

    public function updatedImportFile()
    {
        $this->import();
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls'
        ]);

        try {

            Excel::import(new ProductsImport, $this->importFile->getRealPath());
            $this->reset('importFile');
            $this->resetPage();

            $this->dispatch('swal',
                icon: 'success',
                title: 'Import Success',
                text: 'Products imported successfully'
            );

        } catch (\Exception $e) {

            $this->dispatch('swal',
                icon: 'error',
                title: 'Import Failed',
                text: $e->getMessage()
            );
        }
    }
}
