<?php

namespace App\Livewire\Report\Receivable;

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

    public function getDataProperty()
    {
        $query = DB::table('sales')
            ->join('patients', 'patients.id', '=', 'sales.patient_id')
            ->select(
                'patients.id',
                'patients.name',
                DB::raw('SUM(sales.grand_total) as total_invoice'),
                DB::raw('SUM(sales.paid_total) as total_paid'),
                DB::raw('SUM(sales.balance) as total_outstanding')
            )
            ->groupBy('patients.id', 'patients.name')
            ->havingRaw('SUM(sales.balance) > 0');

        if ($this->search) {
            $query->where('patients.name', 'like', '%' . $this->search . '%');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
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

    public function render()
    {
        return view('livewire.report.receivable.index', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Receivable Rekap',
            'subtitle' => 'Outstanding receivable per patient'
        ]);
    }
}