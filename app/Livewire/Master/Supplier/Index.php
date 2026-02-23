<?php

namespace App\Livewire\Master\Supplier;

use App\Exports\Master\SuppliersExport;
use App\Exports\Master\SuppliersTemplateExport;
use App\Imports\Master\SuppliersImport;
use App\Models\Master\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $code, $name, $location, $contact, $person_in_charge;
    public $supplierId;
    public $search = '';
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteId;
    public $importFile;

    protected function rules()
    {
        return [
            'code' => [
            'required',
            Rule::unique('suppliers', 'code')
                    ->whereNull('deleted_at')
                    ->ignore($this->supplierId)
            ],
            'name' => 'required',
            'person_in_charge' => 'required',
            'location' => 'required'
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
        $suppliers = Supplier::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orWhere('person_in_charge', 'like', '%' . $this->search . '%')
            ->orWhere('location', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(5);

        return view('livewire.master.supplier.index', compact('suppliers'))
                ->layout('layouts.app', [
                    'title' => 'Master Supplier',
                    'subtitle' => 'Manage supplier data',
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

        Supplier::updateOrCreate(
            ['id' => $this->supplierId],
            [
                'code' => $this->code,
                'name' => $this->name,
                'location' => $this->location,
                'contact' => $this->contact,
                'person_in_charge' => $this->person_in_charge,
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
        $supplier = Supplier::findOrFail($id);

        $this->supplierId = $supplier->id;
        $this->code = $supplier->code;
        $this->name = $supplier->name;
        $this->location = $supplier->location;
        $this->person_in_charge = $supplier->person_in_charge;
        $this->contact = $supplier->contact;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function delete()
    {
        Supplier::find($this->deleteId)?->delete();

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
        $this->supplierId = null;
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
        return Excel::download(new SuppliersExport, 'Suppliers '.$today.'.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new SuppliersTemplateExport,
            'Supplier Template.xlsx'
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

            Excel::import(new SuppliersImport, $this->importFile->getRealPath());
            $this->reset('importFile');
            $this->resetPage();

            $this->dispatch('swal',
                icon: 'success',
                title: 'Import Success',
                text: 'Suppliers imported successfully'
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