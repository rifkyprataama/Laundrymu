@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
            <h2 class="font-bold text-xl text-blue-800">Edit Transaksi #{{ $transaction->id }}</h2>
        </div>
        
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pengerjaan</label>
                    <select name="status" class="w-full bg-yellow-50 border border-yellow-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block p-2.5">
                        <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="process" {{ $transaction->status == 'process' ? 'selected' : '' }}>Sedang Dicuci (Process)</option>
                        <option value="ready" {{ $transaction->status == 'ready' ? 'selected' : '' }}>Siap Ambil (Ready)</option>
                        <option value="taken" {{ $transaction->status == 'taken' ? 'selected' : '' }}>Sudah Diambil (Taken)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="payment_status" class="w-full bg-green-50 border border-green-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block p-2.5">
                        <option value="unpaid" {{ $transaction->payment_status == 'unpaid' ? 'selected' : '' }}>Belum Lunas (Unpaid)</option>
                        <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    </select>
                </div>
            </div>

            <hr class="border-gray-200">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pelanggan</label>
                    <select name="customer_id" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $transaction->customer_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Layanan</label>
                        <select name="service_id" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" {{ $transaction->service_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berat (Kg/Pcs)</label>
                        <input name="weight" type="number" step="0.1" value="{{ $transaction->weight }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 font-medium px-4 py-2 text-sm">Batal</a>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-sm px-6 py-2.5 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection