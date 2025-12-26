@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 print:hidden">
        <a href="{{ route('transactions.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Struk
            </button>
        </div>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-2xl border border-gray-200 relative overflow-hidden print:shadow-none print:border-0 print:p-0">
        
        @if($transaction->payment_status == 'paid')
            <div class="absolute top-4 right-4 border-4 border-green-500 text-green-500 font-black text-4xl px-4 py-1 rounded transform rotate-[-15deg] opacity-20 pointer-events-none">
                LUNAS
            </div>
        @else
            <div class="absolute top-4 right-4 border-4 border-red-500 text-red-500 font-black text-4xl px-4 py-1 rounded transform rotate-[-15deg] opacity-20 pointer-events-none">
                UNPAID
            </div>
        @endif

        <div class="text-center border-b-2 border-gray-800 pb-6 mb-6">
            <h1 class="font-black text-3xl uppercase tracking-widest text-gray-900">LAUNDRY PRO</h1>
            <p class="text-sm text-gray-600 mt-1">Jl. Teknologi No. 12, Bandung Jawa Barat</p>
            <p class="text-sm text-gray-600">WhatsApp: 0812-3456-7890</p>
        </div>

        <div class="flex justify-between items-start mb-8">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">No. Order</p>
                <p class="font-mono font-bold text-xl text-gray-800">#{{ $transaction->id }}</p>
                
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mt-4">Pelanggan</p>
                <p class="font-bold text-gray-800">{{ $transaction->customer->name }}</p>
                <p class="text-sm text-gray-600">{{ $transaction->customer->phone }}</p>

                @if($transaction->delivery_type == 'delivery')
                    <div class="mt-2 bg-gray-50 p-2 rounded border border-gray-200 text-sm max-w-[250px]">
                        <span class="font-bold text-xs text-blue-600 uppercase">📍 Alamat Pengiriman:</span><br>
                        {{ $transaction->customer->address ?? '- Alamat belum diisi -' }}
                    </div>
                @endif
            </div>
            
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Tanggal Masuk</p>
                <p class="font-bold text-gray-800">{{ $transaction->created_at->format('d M Y, H:i') }}</p>

                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mt-4">Status Cucian</p>
                <span class="inline-block bg-gray-800 text-white text-xs px-2 py-1 rounded uppercase font-bold">
                    {{ $transaction->status }}
                </span>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead class="bg-gray-100 text-gray-600 border-y border-gray-300">
                <tr>
                    <th class="text-left py-3 px-2">Keterangan Layanan</th>
                    <th class="text-center py-3 px-2">Berat/Qty</th>
                    <th class="text-right py-3 px-2">Harga</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <tr class="border-b border-gray-100">
                    <td class="py-3 px-2">
                        <span class="font-bold">{{ $transaction->service->name }}</span>
                        <div class="text-xs text-gray-500">
                            @if($transaction->service->unit == 'kg')
                                Harga per kg: Rp {{ number_format($transaction->service->price) }}
                            @else
                                Harga satuan: Rp {{ number_format($transaction->service->price) }}
                            @endif
                        </div>
                    </td>
                    <td class="text-center py-3 px-2">{{ $transaction->weight }} {{ $transaction->service->unit }}</td>
                    <td class="text-right py-3 px-2 font-mono">
                        Rp {{ number_format($transaction->service->price * $transaction->weight, 0, ',', '.') }}
                    </td>
                </tr>

                @if($transaction->discount_amount > 0)
                <tr class="text-green-600 italic">
                    <td class="py-2 px-2">Diskon Member (10%)</td>
                    <td class="text-center py-2 px-2"></td>
                    <td class="text-right py-2 px-2 font-mono">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($transaction->delivery_type == 'delivery' && $transaction->delivery_fee > 0)
                <tr class="text-blue-600">
                    <td class="py-2 px-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Biaya Pengiriman
                    </td>
                    <td class="text-center py-2 px-2"></td>
                    <td class="text-right py-2 px-2 font-mono">+ Rp {{ number_format($transaction->delivery_fee, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="flex justify-end border-t-2 border-gray-800 pt-4 mb-12">
            <div class="text-right">
                <p class="text-sm text-gray-500 uppercase tracking-widest mr-2 inline-block align-middle">Total Tagihan</p>
                <span class="text-3xl font-black text-gray-900 inline-block align-middle">
                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 text-center text-xs text-gray-500 mt-12">
            <div>
                <p class="mb-16">Hormat Kami,</p>
                <div class="border-b border-gray-300 w-2/3 mx-auto"></div>
                <p class="mt-2 font-bold text-gray-700">Admin Laundry Pro</p>
            </div>
            <div>
                <p class="mb-16">Penerima,</p>
                <div class="border-b border-gray-300 w-2/3 mx-auto"></div>
                <p class="mt-2 font-bold text-gray-700">{{ $transaction->customer->name }}</p>
            </div>
        </div>

        <div class="mt-12 text-[10px] text-gray-400 text-center leading-relaxed">
            <p>1. Pengambilan barang wajib menyertakan nota ini.</p>
            <p>2. Barang yang tidak diambil lebih dari 30 hari bukan tanggung jawab kami.</p>
            <p>3. Komplain maksimal 1x24 jam setelah barang diambil.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { margin: 0; size: auto; }
        body { background-color: white; -webkit-print-color-adjust: exact; }
        nav, footer { display: none !important; }
        .print\:hidden { display: none !important; }
        .shadow-2xl { box-shadow: none !important; }
    }
</style>
@endsection