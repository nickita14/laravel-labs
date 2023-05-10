<?php

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

Route::get('/', function () { return view('welcome'); });


Route::get('/cesar', function () { return view('cesar'); });
Route::post('/cesar', 'App\Http\Controllers\CaesarCipherController@cesar')->name('cesar');

Route::get('/playfair', function () { return view('playfair'); });
Route::post('/playfair', 'App\Http\Controllers\PlayfairController@playfair')->name('playfair');

Route::get('/salsa', function () { return view('salsa'); });
Route::post('/salsa', 'App\Http\Controllers\SalsaController@salsa')->name('salsa');