<?php

use App\Models\Master\Patient;
use Illuminate\Support\Facades\Route;
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
use App\Livewire\Master\Product\KnnTest as KNNTest;
use App\Livewire\Auth\Login as LoginComponent;

use App\Http\Controllers\Api\ProductApiController as ProductAPI;
use App\Http\Controllers\Api\SupplierApiController as SupplierAPI;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginComponent::class)->name('login');
});
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', function () {

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');

    })->name('logout');

});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MASTER
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->group(function () {

        Route::get('/products', ProductIndex::class)
            ->middleware('permission:product.view')
            ->name('master.products');

        Route::get('/suppliers', SupplierIndex::class)
            ->middleware('permission:supplier.view')
            ->name('master.suppliers');

        Route::get('/patients', PatientIndex::class)
            ->middleware(['auth', 'permission:patient.view'])
            ->name('master.patients');
    });

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */
    Route::prefix('transaction')->group(function () {

        Route::prefix('purchases')->group(function () {

            Route::get('/', PurchaseIndex::class)
                ->middleware('permission:purchase.view')
                ->name('transaction.purchases');

            Route::get('/create', PurchaseCreate::class)
                ->middleware('permission:purchase.create')
                ->name('transaction.purchases.create');

            Route::get('/edit/{id}', PurchaseEdit::class)
                ->middleware('permission:purchase.edit')
                ->name('transaction.purchases.edit');

            Route::get('/payment/{id}', PurchasePayment::class)
                ->middleware('permission:purchase.create')
                ->name('transaction.purchases.payment');

            Route::get('/transaction/purchases/{id}',PurchaseShow::class)
                ->middleware('permission:purchase.view')
                ->name('transaction.purchases.show');

        });

    });

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT
    |--------------------------------------------------------------------------
    */
    Route::prefix('account')->group(function () {

        Route::get('/roles', RoleIndex::class)
            ->middleware('permission:view role')
            ->name('account.roles');

        Route::get('/users', UserIndex::class)
            ->middleware('permission:view user')
            ->name('account.users');
    });

    /*
    |--------------------------------------------------------------------------
    | KNN TEST
    |--------------------------------------------------------------------------
    */
    Route::get('/knn-test', KNNTest::class)
        ->middleware('permission:test knn')
        ->name('test.knn');
});

/*
|--------------------------------------------------------------------------
| API (Internal Web API)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    Route::get('/products/purchase',
        [ProductAPI::class, 'purchaseFind']
    )->name('api.products.purchase');

    Route::get('/suppliers/purchase',
        [SupplierAPI::class, 'purchaseFind']
    )->name('api.suppliers.purchase');

});