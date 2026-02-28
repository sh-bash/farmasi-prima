<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Livewire Components
|--------------------------------------------------------------------------
*/

use App\Livewire\Auth\Login as LoginComponent;

use App\Livewire\Master\Product\Index as ProductIndex;
use App\Livewire\Master\Supplier\Index as SupplierIndex;
use App\Livewire\Master\Patient\Index as PatientIndex;

use App\Livewire\Account\Role\Index as RoleIndex;
use App\Livewire\Account\User\Index as UserIndex;

use App\Livewire\Transaction\Purchase\Index as PurchaseIndex;
use App\Livewire\Transaction\Purchase\Create as PurchaseCreate;
use App\Livewire\Transaction\Purchase\Edit as PurchaseEdit;
use App\Livewire\Transaction\Purchase\Payment as PurchasePayment;
use App\Livewire\Transaction\Purchase\Show as PurchaseShow;

use App\Livewire\Transaction\Sale\Index as SaleIndex;
use App\Livewire\Transaction\Sale\Create as SaleCreate;
use App\Livewire\Transaction\Sale\Edit as SaleEdit;
use App\Livewire\Transaction\Sale\Payment as SalePayment;
use App\Livewire\Transaction\Sale\Show as SaleShow;

use App\Livewire\Report\Purchase\Index as PurchaseReportIndex;
use App\Livewire\Report\Sale\Index as SaleReportIndex;
use App\Livewire\Report\Stock\Index as StockIndex;
use App\Livewire\Report\Stock\Detail as StockDetail;
use App\Livewire\Report\Receivable\Index as ReceivableIndex;
use App\Livewire\Report\Receivable\Detail as ReceivableDetail;
use App\Livewire\Report\Payable\Index as PayableIndex;
use App\Livewire\Report\Payable\Detail as PayableDetail;

use App\Livewire\Master\Product\KnnTest as KNNTest;

/*
|--------------------------------------------------------------------------
| API Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\ProductApiController as ProductAPI;
use App\Http\Controllers\Api\SupplierApiController as SupplierAPI;
use App\Http\Controllers\Api\PatientApiController as PatientAPI;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginComponent::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| PROTECTED
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MASTER
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/products', ProductIndex::class)->middleware('permission:product.view')->name('products');
        Route::get('/suppliers', SupplierIndex::class)->middleware('permission:supplier.view')->name('suppliers');
        Route::get('/patients', PatientIndex::class)->middleware('permission:patient.view')->name('patients');
    });

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */
    Route::prefix('transaction')->name('transaction.')->group(function () {

        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', PurchaseIndex::class)->middleware('permission:purchase.view')->name('index');
            Route::get('/create', PurchaseCreate::class)->middleware('permission:purchase.create')->name('create');
            Route::get('/edit/{id}', PurchaseEdit::class)->middleware('permission:purchase.edit')->name('edit');
            Route::get('/payment/{id}', PurchasePayment::class)->middleware('permission:purchase.create')->name('payment');
            Route::get('/show/{id}', PurchaseShow::class)->middleware('permission:purchase.view')->name('show');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', SaleIndex::class)->middleware('permission:sales.view')->name('index');
            Route::get('/create', SaleCreate::class)->middleware('permission:sales.create')->name('create');
            Route::get('/edit/{id}', SaleEdit::class)->middleware('permission:sales.edit')->name('edit');
            Route::get('/payment/{id}', SalePayment::class)->middleware('permission:sales.create')->name('payment');
            Route::get('/show/{id}', SaleShow::class)->middleware('permission:sales.view')->name('show');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | REPORT
    |--------------------------------------------------------------------------
    */
    Route::prefix('report')->name('report.')->group(function () {

        Route::get('/purchase', PurchaseReportIndex::class)->middleware('permission:purchase.view')->name('purchase');
        Route::get('/sale', SaleReportIndex::class)->middleware('permission:sales.view')->name('sale');

        Route::get('/stock', StockIndex::class)->middleware('permission:product.view')->name('stock');
        Route::get('/stock/{productId}', StockDetail::class)->middleware('permission:product.view')->name('stock.detail');

        Route::get('/receivable', ReceivableIndex::class)->middleware('permission:sales.view')->name('receivable');
        Route::get('/receivable/{patientId}', ReceivableDetail::class)->middleware('permission:sales.view')->name('receivable.detail');

        Route::get('/payable', PayableIndex::class)->middleware('permission:purchase.view')->name('payable');
        Route::get('/payable/{supplierId}', PayableDetail::class)->middleware('permission:purchase.view')->name('payable.detail');
    });

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT
    |--------------------------------------------------------------------------
    */
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/roles', RoleIndex::class)->middleware('permission:view role')->name('roles');
        Route::get('/users', UserIndex::class)->middleware('permission:view user')->name('users');
    });

    /*
    |--------------------------------------------------------------------------
    | KNN TEST
    |--------------------------------------------------------------------------
    */
    Route::get('/knn-test', KNNTest::class)->name('test.knn');
});

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {

    Route::get('/products/purchase', [ProductAPI::class, 'purchaseFind'])->name('products.purchase');
    Route::get('/products/sale', [ProductAPI::class, 'saleFind'])->name('products.sale');

    Route::get('/suppliers/purchase', [SupplierAPI::class, 'purchaseFind'])->name('suppliers.purchase');
    Route::get('/patients/sales', [PatientAPI::class, 'saleFind'])->name('patients.sale');
});