<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Expense; 
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $incomeToday = Transaction::whereDate('paid_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $expenseToday = Expense::whereDate('expense_date', $today)
            ->sum('amount');

        $profitToday = $incomeToday - $expenseToday;
        $warningCount = Transaction::whereDate('deadline', $today)
            ->whereNotIn('status', ['done', 'taken'])
            ->count();

        $processCount = Transaction::where('status', 'process')->count();
        $readyCount = Transaction::where('status', 'ready')->count();
        $totalMember = Customer::count();

        $recentTransactions = Transaction::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'incomeToday', 
            'expenseToday', 
            'profitToday', 
            'warningCount',
            'processCount', 
            'readyCount', 
            'totalMember',
            'recentTransactions'
        ));
    }
}