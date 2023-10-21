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


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Transaction\TransactionController;

// auth
Route::post('/users', [AuthController::class, 'createUser']);
Route::post('/login', [AuthController::class, 'userLogin']);


// transactions

Route::get('/', [TransactionController::class, 'allTransactions']);
// deposit
Route::get('/deposit', [TransactionController::class, 'allDeposit']);
Route::post('/deposit', [TransactionController::class, 'depositTrans']);

// withdrawal
Route::get('/withdrawal', [TransactionController::class, 'allwithdrawal']);
Route::post('/withdrawal', [TransactionController::class, 'withdrawTrans']);
