<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'service'])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->whereHas('customer', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->paginate(10);
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
            'weight' => 'required|numeric|min:0.1',
            'delivery_type' => 'required',
            'delivery_fee' => 'numeric|min:0',
            'payment_status' => 'required',
            'payment_method' => 'required_if:payment_status,paid', 
            'deadline' => 'nullable|date',  
            'created_at' => 'nullable|date' 
        ], [
            'payment_method.required_if' => 'Jika status Lunas, mohon pilih metode pembayarannya (Tunai/Transfer/QRIS).'
        ]);

        $service = Service::find($request->service_id);
        $customer = Customer::find($request->customer_id);

        $basic_price = $service->price * $request->weight;
        
        $discount = 0;
        if ($customer->is_member) {
            $discount = $basic_price * 0.10;
        }

        $delivery_fee = ($request->delivery_type == 'delivery') ? $request->delivery_fee : 0;
        $total_price = ($basic_price - $discount) + $delivery_fee;

        $paidTime = null;
        if ($request->payment_status == 'paid') {
            $paidTime = $request->created_at ?? now();
        }

        Transaction::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'weight' => $request->weight,
            'total_price' => $total_price,
            'discount_amount' => $discount,
            'delivery_type' => $request->delivery_type,
            'delivery_fee' => $delivery_fee,
            'status' => 'pending', 
            'payment_status' => $request->payment_status,
            'payment_method' => ($request->payment_status == 'paid') ? $request->payment_method : null,
            'paid_at' => $paidTime, 
            'notes' => $request->notes, 
            'deadline' => $request->deadline,
            'created_at' => $request->created_at ?? now() 
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibuat!');
    }

    public function edit(Transaction $transaction)
    {
        $customers = Customer::all();
        $services = Service::all();
        
        return view('transactions.edit', compact('transaction', 'customers', 'services'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'customer_id' => 'required',
            'service_id' => 'required',
            'weight' => 'required|numeric|min:0.1',
            'status' => 'required',
            'payment_status' => 'required',
            'payment_method' => 'required_if:payment_status,paid', 
            'delivery_type' => 'required',
            'delivery_fee' => 'numeric|min:0',
            'created_at' => 'required|date' 
        ]);

        $isPriceDataChanged = (
            $request->service_id != $transaction->service_id ||
            $request->weight != $transaction->weight ||
            $request->delivery_type != $transaction->delivery_type ||
            $request->delivery_fee != $transaction->delivery_fee ||
            $request->customer_id != $transaction->customer_id
        );

        if ($isPriceDataChanged) {
            $service = Service::find($request->service_id);
            $customer = Customer::find($request->customer_id);

            $basic_price = $service->price * $request->weight;
            
            $discount = 0;
            if ($customer->is_member) {
                $discount = $basic_price * 0.10;
            }

            $delivery_fee = ($request->delivery_type == 'delivery') ? $request->delivery_fee : 0;
            $total_price = ($basic_price - $discount) + $delivery_fee;
        } else {
            $total_price = $transaction->total_price;
            $discount = $transaction->discount_amount;
            $delivery_fee = $transaction->delivery_fee;
        }

        $paymentStatus = $request->payment_status;
        $pickupTime = $transaction->picked_up_at;
        $paidTime = $transaction->paid_at; 

        if ($request->status == 'taken') {
            $paymentStatus = 'paid';
            if ($pickupTime == null) $pickupTime = now();
        }

        if ($transaction->payment_status == 'unpaid' && $paymentStatus == 'paid') {
            $paidTime = now(); 
        }

        $paymentMethod = ($paymentStatus == 'paid') ? $request->payment_method : null;

        $transaction->update([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'weight' => $request->weight,
            'total_price' => $total_price,
            'discount_amount' => $discount,
            'delivery_type' => $request->delivery_type,
            'delivery_fee' => $delivery_fee,
            'status' => $request->status,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
            'paid_at' => $paidTime,
            'picked_up_at' => $pickupTime,
            'created_at' => $request->created_at 
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->status != 'pending') {
            return back()->with('error', 'Gagal hapus! Transaksi sedang diproses atau sudah selesai.');
        }

        if ($transaction->payment_status == 'paid') {
            return back()->with('error', 'Gagal hapus! Transaksi sudah lunas. Uang tidak boleh hilang dari pembukuan.');
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus (Data Bersih).');
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }
}