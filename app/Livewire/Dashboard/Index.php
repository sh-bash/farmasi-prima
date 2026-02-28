<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $isPatient = false;

    // Admin Data
    public $pendingOrders;
    public $newPatients;
    public $totalOrders;
    public $outOfStockProducts;
    public $totalSales;
    public $totalPurchases;
    public $totalReceivable;
    public $totalPayable;

    // Patient Data
    public $patientOutstanding = 0;
    public $patientTotalOrders = 0;

    public function mount()
    {
        $user = auth()->user();

        // =========================
        // PATIENT DASHBOARD
        // =========================
        if ($user->hasRole('patient')) {

            $this->isPatient = true;

            $patient = $user->patient;

            $this->patientOutstanding = DB::table('sales')
                ->where('patient_id', $patient->id)
                ->sum('balance');

            $this->patientTotalOrders = DB::table('sales')
                ->where('patient_id', $patient->id)
                ->count();

            return; // stop disini, jangan load admin data
        }

        // =========================
        // ADMIN DASHBOARD
        // =========================

        $this->pendingOrders = DB::table('sales')
            ->where('status', 'posted')
            ->count();

        $this->newPatients = DB::table('patients')
            ->whereMonth('created_at', now()->month)
            ->count();

        $this->totalSales = DB::table('sales')->sum('grand_total');
        $this->totalPurchases = DB::table('purchases')->sum('grand_total');
        $this->totalReceivable = DB::table('sales')->sum('balance');
        $this->totalPayable = DB::table('purchases')->sum('balance');

        $this->totalOrders = DB::table('sales')->count();

        $this->outOfStockProducts = DB::table('products')
            ->leftJoin('purchase_details as pd', 'pd.product_id', '=', 'products.id')
            ->leftJoin('sale_details as sd', 'sd.product_id', '=', 'products.id')
            ->select(
                'products.id',
                DB::raw('(COALESCE(SUM(pd.qty),0) - COALESCE(SUM(sd.qty),0)) as current_stock')
            )
            ->groupBy('products.id')
            ->havingRaw('current_stock <= 0')
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('layouts.app', [
                'title' => 'Dashboard',
                'subtitle' => 'System Overview'
            ]);
    }
}