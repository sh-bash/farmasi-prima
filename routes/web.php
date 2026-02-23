<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Master\Product\Index as ProductIndex;
use App\Livewire\Master\Supplier\Index as SupplierIndex;
use App\Livewire\Account\Role\Index as RoleIndex;
use App\Livewire\Account\User\Index as UserIndex;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('master')->group(function () {

    Route::get('/products', ProductIndex::class)->name('master.products');
    Route::get('/suppliers', SupplierIndex::class)->name('master.suppliers');
    // Route::get('/patients', PatientIndex::class)->name('master.patients');

});

Route::prefix('account')->group(function () {
    Route::get('/roles', RoleIndex::class)->name('account.roles');
    Route::get('/users', UserIndex::class)->name('account.users');

});