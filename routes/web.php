<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('transactions.index');
});

Route::resource('transactions', TransactionController::class);
