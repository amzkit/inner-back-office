<?php
namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('import/sales', [UploadController::class, 'sales']);

Route::get('stalls', [DataController::class, 'stalls']);
Route::get('teams', [DataController::class, 'teams']);

Route::get('stall/sales', [DataController::class, 'stall_sales']);
Route::get('team/sales', [DataController::class, 'team_sales']);

Route::get('people', [PeopleController::class, 'index']);
Route::get('people/{id}', [PeopleController::class, 'show']);
Route::post('people/store', [PeopleController::class, 'store']);
