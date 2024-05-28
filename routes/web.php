<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::prefix('/admin')
    ->middleware('auth', 'admin')
    ->group(function () {
        Route::get('/', 'HomeController@index');
        Route::resource('user', 'UserController');
        Route::resource('product', 'ProductController');
        Route::resource('product-category', 'ProductCategoryController');
        Route::resource('customer', 'CustomerController');
        Route::resource('coupon', 'CouponController');

        Route::get('/company', 'CompanyProfileController@index')->name('companyProfile.index');
        Route::post('/company', 'CompanyProfileController@save')->name('companyProfile.save');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::post('/sale/getCoupon', 'SaleController@getCoupon')->name('sale.getCoupon');
    Route::resource('sale', 'SaleController');
    Route::post('/transaction/storeTransaction', 'TransactionController@storeTransaction')->name('transaction.storeTransaction');
    Route::post('/transaction/report', 'TransactionController@report')->name('transaction.report');
    //Route::get('/struk/{transaction_code?}', 'TransactionController@struk')->name('transaction.struk');
    Route::resource('transaction', 'TransactionController')->except([
        'create'
    ]);
    Route::get('transaction/{transaction_code}/details', [TransactionController::class, 'showDetails'])->name('transaction.details');
    Route::post('transaction/{transaction_code}/update-status', [TransactionController::class, 'updateStatus'])->name('transaction.updateStatus');

    Route::get('/transaction/create/{transaction_code?}', 'TransactionController@create')->name('transaction.create');
    Route::get('purchases', 'PurchasesController@index')->name('purchases.index');
    Route::get('purchases/create', 'PurchasesController@create')->name('purchases.create');
    Route::post('purchases', 'PurchasesController@store')->name('purchases.store');
    Route::get('purchases/{id}', 'PurchasesController@show')->name('purchases.show');
    Route::get('purchases/{id}/edit', 'PurchasesController@edit')->name('purchases.edit');
    Route::put('purchases/{id}', 'PurchasesController@update')->name('purchases.update');
    Route::delete('purchases/{id}', 'PurchasesController@destroy')->name('purchases.destroy');

    Route::get('suppliers', 'SuppliersController@index')->name('suppliers.index');
    Route::get('suppliers/create', 'SuppliersController@create')->name('suppliers.create');
    Route::post('suppliers', 'SuppliersController@store')->name('suppliers.store');
    Route::get('suppliers/{id}', 'SuppliersController@show')->name('suppliers.show');
    Route::get('suppliers/{id}/edit', 'SuppliersController@edit')->name('suppliers.edit');
    Route::put('suppliers/{id}', 'SuppliersController@update')->name('suppliers.update');
    Route::delete('suppliers/{id}', 'SuppliersController@destroy')->name('suppliers.destroy');

    Route::get('/profile', 'ProfileController@index')->name('profile.index');
    Route::put('/profile', 'ProfileController@update')->name('profile.update');
});

Auth::routes(['verify' => true]);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');
