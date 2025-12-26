<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
            })
            ->withCount('transactions')
            ->latest()
            ->paginate(10); 

        return view('customers.index', compact('customers'));
    }

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

        $fullAddress = $request->address;
        if($request->landmark) {
            $fullAddress .= " (Patokan: " . $request->landmark . ")";
        }

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $fullAddress,
            'is_member' => $request->has('is_member') 
        ]);

        if($request->input('from') == 'transaction') {
            return redirect()
                ->route('transactions.create')
                ->with('success', 'Pelanggan berhasil dibuat! Silakan lanjut transaksi.')
                ->with('new_customer_id', $customer->id); 
        }

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil disimpan');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required', 
            'address' => 'required',
            'notes' => 'nullable|string'
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'notes' => $request->notes,
            'is_member' => $request->boolean('is_member') 
        ]);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        if($customer->transactions()->count() > 0) {
            return back()->with('error', 'Gagal hapus! Pelanggan ini memiliki riwayat transaksi.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}