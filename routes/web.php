<?php

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('payment');
});
// // Route untuk menampilkan halaman pilihan metode pembayaran
Route::get('select-payment-method', function () { 
    return view('select-payment-method');
});
// Route untuk menangani pilihan metode pembayaran dan mengarahkan ke halaman proses pembayaran
Route::post('/select-payment-method', [PaymentMethodController::class, 'select'])->name('select-payment-method');