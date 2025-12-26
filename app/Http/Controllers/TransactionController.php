<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'service'])
                        ->latest()
                        ->get();
                        
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $customers = Customer::all();
        $services = Service::all();
        
        return view('transactions.create', compact('customers', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'service_id' => 'required',
            'weight' => 'required|numeric|min:1',
        ]);

        $service = Service::find($request->service_id);
        $customer = Customer::find($request->customer_id);
        $basic_price = $service->price * $request->weight;
        $discount = 0;
        if ($customer->is_member) {
            $discount = $basic_price * 0.10; 
        }
        $total_price = $basic_price - $discount;

        Transaction::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'weight' => $request->weight,
            'total_price' => $total_price,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibuat!');
    }
}