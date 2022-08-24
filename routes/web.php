<?php

use App\Http\Controllers\Pages;
use App\Http\Controllers\Files;
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

Route::get('/', [Pages::class, 'index']);




//---- Form Routes
Route::post('import', [Files::class, '_import']);


//---- Direct to views
Route::prefix('/')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    });

    Route::get('report', function () {
        $components['title'] = "Report";
        $components['nav']   = "report";
        return view('pages.report', $components);
    });
});
