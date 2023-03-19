<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthenticationController;

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


Route::get('/tests', [Controller::class, 'test'])->name('dev-test');
Route::get('', function () {
    return response([
        'message' => 'Please Find the Documentation @ https://documenter.getpostman.com/view/17249421/2s93JzLfvt'
    ], 200);
});
Route::post('/authenticate', [AuthenticationController::class, 'login'])->name('login.api');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/revoke', [AuthenticationController::class, 'logout'])->name('logout.api');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index.api');

    Route::get('/accounts', [AccountController::class, 'index'])->name('account.index.api');
    Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('account.detail.api');
    Route::get('/accounts/{account}/balance', [AccountController::class, 'showBalance'])->name('account.balance.api');
    Route::get('/accounts/{account}/history', [AccountController::class, 'transferHistory'])
        ->name('account.history.api');
    Route::post('/accounts', [AccountController::class, 'store'])->name('account.create.api');




    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transaction.index.api');
    Route::post('/transactions', [TransactionController::class, 'create'])
        ->name('transaction.create.api');
});
