<?php

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

Route::get('/', function () {
    return view('welcome');
});

//Route::get('book', 'BookController@index');

Route::resource('book', 'App\Http\Controllers\BookController');

Route::get('fd', 'App\Http\Controllers\FdController@index');
Route::get('fd/console', 'App\Http\Controllers\FdController@console');
Route::get('fd/console/{page}', 'App\Http\Controllers\FdController@console');
Route::get('fd/traffic', 'App\Http\Controllers\FdController@traffic');
