<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/home');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/add-to-cart/{product}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add')->middleware('auth');

Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index')->middleware('auth');

Route::get('/cart/destroy/{itemId}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy')->middleware('auth');

Route::get('/cart/update/{itemId}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update')->middleware('auth');

Route::get('/cart/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout')->middleware('auth');

Route::resource('orders', OrderController::class)->only('store')->middleware('auth');




Route::get('paypal/checkout/{order}', [App\Http\Controllers\PayPalController::class, 'getExpressCheckout'])->name('paypal.checkout');

Route::get('paypal/checkout-success/{order}', [App\Http\Controllers\PayPalController::class, 'getExpressCheckoutSuccess'])->name('paypal.success');

Route::get('paypal/checkout-cancel', [App\Http\Controllers\PayPalController::class, 'cancelPage'])->name('paypal.cancel');



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
