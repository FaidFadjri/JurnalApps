<?php

use App\Http\Controllers\Pages;
use App\Http\Controllers\Files;
use App\Http\Controllers\Operation;
use Illuminate\Support\Facades\Route;

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

//--- Main apps
Route::get('/', [Pages::class, 'index']);
Route::get('report', [Pages::class, 'report']);

//---- Datatables
Route::get('load_transaksi', [Operation::class, '_loadTransaksi'])->name('datatable');
Route::post('detail_transaksi', [Operation::class, '_detailTransaksi'])->name('detail');



//---- Ajax Routes
Route::post('search', [Operation::class, '_searchPKB'])->name('search');

//---- Routes Form
Route::post('import', [Files::class, '_import']);

//---- Direct to views
Route::get('login', function () {
    return view('auth.login');
});
