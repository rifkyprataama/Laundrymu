<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $incomeToday = Transaction::whereDate('created_at', $today)->sum('total_price');

        $incomeMonth = Transaction::whereMonth('created_at', date('m'))->sum('total_price');

        $activeTransactions = Transaction::whereIn('status', ['pending', 'process'])->count();

        $totalMembers = Customer::where('is_member', true)->count();

        $recentTransactions = Transaction::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact('incomeToday', 'incomeMonth', 'activeTransactions', 'totalMembers', 'recentTransactions'));
    }
}