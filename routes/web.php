<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Master\Product\Index as ProductIndex;
use App\Livewire\Master\Supplier\Index as SupplierIndex;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('master')->group(function () {

    Route::get('/products', ProductIndex::class)->name('master.products');
    Route::get('/suppliers', SupplierIndex::class)->name('master.suppliers');
    // Route::get('/patients', PatientIndex::class)->name('master.patients');

});
