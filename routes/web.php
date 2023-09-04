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



Auth::routes();

Route::middleware('auth')->group(function(){
    Route::get('/', function () {   return view('index');} );
    Route::get('/import', [App\Http\Controllers\HomeController::class, 'import'])->name('import');
    Route::get('/people', [App\Http\Controllers\HomeController::class, 'people'])->name('people');
    Route::get('/people/{id}', [App\Http\Controllers\HomeController::class, 'people_show'])->name('people.show');
});
