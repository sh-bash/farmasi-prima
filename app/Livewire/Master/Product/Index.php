<?php

namespace App\Livewire\Master\Product;

use App\Exports\Master\ProductsExport;
use App\Exports\Master\ProductsTemplateExport;
use App\Imports\Master\ProductsImport;
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
            'name' => 'required',
            'het' => 'required|numeric'
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
            ->paginate(5);

        return view('livewire.master.product.index', compact('products'))
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

    public function save()
    {
        $this->validate();

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'barcode' => $this->barcode,
                'name' => $this->name,
                'het' => $this->het,
            ]
        );

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
        $product = Product::findOrFail($id);

        $this->productId = $product->id;
        $this->barcode = $product->barcode;
        $this->name = $product->name;
        $this->het = $product->het;
        $this->isEdit = true;
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
        $this->barcode = '';
        $this->name = '';
        $this->het = '';
        $this->productId = null;
        $this->isEdit = false;
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