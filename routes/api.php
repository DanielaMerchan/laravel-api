<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/auth', [App\Http\Controllers\UserController::class, 'authenticate']);


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/factura', [App\Http\Controllers\Api\v1\FacturaController::class, 'index'])->name('factura');
    Route::get('/factura/{id}', [App\Http\Controllers\Api\v1\FacturaController::class, 'show'])->name('get_factura');
    Route::post('/factura', [App\Http\Controllers\Api\v1\FacturaController::class, 'store'])->name('post_factura');
    Route::put('/factura/{id}', [App\Http\Controllers\Api\v1\FacturaController::class, 'update'])->name('update_factura');
    Route::delete('/factura/{id}', [App\Http\Controllers\Api\v1\FacturaController::class, 'destroy'])->name('delete_factura');
});
