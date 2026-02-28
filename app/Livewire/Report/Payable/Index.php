<?php

namespace App\Livewire\Report\Payable;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'total_outstanding';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function getDataProperty()
    {
        $query = DB::table('purchases')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('SUM(purchases.grand_total) as total_invoice'),
                DB::raw('SUM(purchases.paid_total) as total_paid'),
                DB::raw('SUM(purchases.balance) as total_outstanding')
            )
            ->groupBy('suppliers.id', 'suppliers.name')
            ->havingRaw('SUM(purchases.balance) > 0');

        if ($this->search) {
            $query->where('suppliers.name', 'like', '%' . $this->search . '%');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function render()
    {
        return view('livewire.report.payable.index', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Payable Rekap',
            'subtitle' => 'Outstanding payable per supplier'
        ]);
    }
}