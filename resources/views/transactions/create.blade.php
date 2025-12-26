@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-xl text-gray-800">Buat Transaksi Baru</h2>
            <p class="text-sm text-gray-500">Silakan isi data cucian pelanggan.</p>
        </div>
        
        <form action="{{ route('transactions.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-gray-700">Pilih Pelanggan</label>
                    <a href="{{ route('customers.create') }}" class="text-xs text-blue-600 hover:text-blue-800 font-bold hover:underline">
                        + Pelanggan Baru
                    </a>
                </div>
                <div class="relative">
                    <select name="customer_id" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">-- Cari Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->name }} ({{ $c->is_member ? '⭐ Member' : 'Umum' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Layanan</label>
                <select name="service_id" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    @foreach($services as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->name }} — Rp {{ number_format($s->price) }} / {{ $s->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Berat / Jumlah</label>
                <div class="flex">
                    <input name="weight" type="number" step="0.1" class="rounded-none rounded-l-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5" placeholder="Contoh: 3.5">
                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-md font-bold">
                        Kg/Pcs
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500">*Gunakan titik (.) untuk desimal, misal: 2.5</p>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 font-medium px-4 py-2 text-sm">Batal</a>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-sm px-6 py-2.5 transition">
                    Simpan & Hitung Harga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection