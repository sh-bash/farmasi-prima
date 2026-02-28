<?php

namespace App\Livewire\Report\Stock;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $sortField = 'p.name';
    public $sortDirection = 'asc';

    public $search = '';

    public function getDataProperty()
    {
        return DB::table('products as p')
            ->select(
                'p.id',
                'p.name',
                DB::raw('COALESCE(SUM(pd.qty),0) as stock_in'),
                DB::raw('COALESCE(SUM(sd.qty),0) as stock_out'),
                DB::raw('(COALESCE(SUM(pd.qty),0) - COALESCE(SUM(sd.qty),0)) as current_stock')
            )
            ->leftJoin('purchase_details as pd', 'pd.product_id', '=', 'p.id')
            ->leftJoin('sale_details as sd', 'sd.product_id', '=', 'p.id')
            ->when($this->search, function ($q) {
                $q->where('p.name', 'like', '%' . $this->search . '%');
            })
            ->groupBy('p.id', 'p.name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.report.stock.index', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Stock Rekap',
            'subtitle' => 'Current stock summary per product'
        ]);
    }
}