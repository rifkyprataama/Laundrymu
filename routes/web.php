<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('transactions', TransactionController::class);
Route::get('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
Route::resource('customers', \App\Http\Controllers\CustomerController::class);
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'destroy']);