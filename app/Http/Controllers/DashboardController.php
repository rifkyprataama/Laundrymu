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
        $incomeToday = Transaction::whereDate('paid_at', today()) 
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $cashToday = Transaction::whereDate('paid_at', today()) 
            ->where('payment_status', 'paid')
            ->where('payment_method', 'cash')
            ->sum('total_price');
            
        $transferToday = Transaction::whereDate('paid_at', today())
            ->where('payment_status', 'paid')
            ->whereIn('payment_method', ['transfer', 'qris'])
            ->sum('total_price');

        $unpaidTotal = Transaction::where('payment_status', 'unpaid')->sum('total_price');

        $processCount = Transaction::where('status', 'process')->count();
        $readyCount = Transaction::where('status', 'ready')->count();
        $memberCount = Customer::where('is_member', true)->count();

        $urgentCount = Transaction::where('status', '!=', 'taken')
            ->whereDate('deadline', '<=', now())
            ->count();

        $recentTransactions = Transaction::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'incomeToday', 'cashToday', 'transferToday', 
            'unpaidTotal', 
            'processCount', 'readyCount', 'urgentCount',
            'memberCount', 'recentTransactions'
        ));
    }
}