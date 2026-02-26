<?php

namespace App\Livewire\Transaction\Purchase;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction\Purchase;

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
        $purchases = Purchase::with('supplier', 'details.product', 'payments')
            ->where(function ($q) {
                $q->where('purchase_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.transaction.purchase.index', compact('purchases'))
            ->layout('layouts.app', [
                'title' => 'Purchase',
                'subtitle' => 'Manage purchase transactions',
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
            title: 'Delete Purchase?',
            text: 'This purchase will be moved to trash'
        );
    }

    public function delete()
    {
        Purchase::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'Purchase deleted successfully'
        );
    }
}