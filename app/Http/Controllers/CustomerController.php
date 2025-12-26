<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,phone', 
            'address' => 'required'
        ]);

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_member' => false 
        ]);

        return redirect()->route('transactions.create')
            ->with('success', 'Pelanggan baru berhasil ditambahkan! Silakan pilih di daftar.');
    }
}