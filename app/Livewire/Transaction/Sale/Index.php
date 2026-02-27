<?php

namespace App\Livewire\Transaction\Sale;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction\Sale;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $showTable = true;
    public $deleteId;
    public $expanded = null;

    public function render()
    {
        $sales = Sale::with('patient', 'details.product', 'payments')
            ->where(function ($q) {
                $q->where('sale_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.transaction.sale.index', compact('sales'))
            ->layout('layouts.app', [
                'title' => 'Sale',
                'subtitle' => 'Manage sale transactions',
            ]);
    }

    public function toggleDetail($id)
    {
        $this->expanded = $this->expanded === $id ? null : $id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function refreshData()
    {
        $this->resetPage();
    }

    public function toggleTable()
    {
        $this->showTable = !$this->showTable;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal-confirm',
            title: 'Delete Sale?',
            text: 'This sale will be moved to trash'
        );
    }

    public function delete()
    {
        Sale::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'Sale deleted successfully'
        );
    }
}