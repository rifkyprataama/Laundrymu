@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex justify-between items-center mb-6 print:hidden">
        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg">
            Cetak Struk
        </button>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 print:shadow-none print:border-0">
        <div class="text-center border-b border-gray-200 pb-4 mb-4">
            <h1 class="font-bold text-2xl uppercase tracking-widest text-gray-800">Laundry Pro</h1>
            <p class="text-sm text-gray-500">Jl. Teknologi No. 12, Bandung</p>
            <p class="text-sm text-gray-500">WA: 0812-3456-7890</p>
        </div>

        <div class="flex justify-between text-sm mb-4">
            <div>
                <p class="text-gray-500">No. Order</p>
                <p class="font-bold">#{{ $transaction->id }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-500">Tanggal</p>
                <p class="font-bold">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="mb-6 p-3 bg-gray-50 rounded-lg print:bg-transparent print:p-0">
            <p class="text-xs text-gray-500 uppercase font-bold">Pelanggan</p>
            <p class="font-bold text-lg">{{ $transaction->customer->name }}</p>
            <p class="text-sm text-gray-600">{{ $transaction->customer->phone }}</p>
        </div>

        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="border-b border-gray-300">
                    <th class="text-left py-2">Layanan</th>
                    <th class="text-center py-2">Berat</th>
                    <th class="text-right py-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2">{{ $transaction->service->name }}</td>
                    <td class="text-center py-2">{{ $transaction->weight }} {{ $transaction->service->unit }}</td>
                    <td class="text-right py-2">Rp {{ number_format($transaction->total_price + $transaction->discount_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="border-t border-gray-200 pt-4 space-y-2">
            @if($transaction->discount_amount > 0)
            <div class="flex justify-between text-sm text-red-500">
                <span>Diskon Member (10%)</span>
                <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="flex justify-between text-xl font-bold text-gray-900">
                <span>Total Bayar</span>
                <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            <p>Terima kasih telah mempercayakan cucian Anda kepada kami.</p>
            <p class="mt-1">Barang yang tidak diambil > 30 hari bukan tanggung jawab kami.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { margin: 0; }
        body { background-color: white; -webkit-print-color-adjust: exact; }
        nav, footer { display: none; } 
    }
</style>
@endsection