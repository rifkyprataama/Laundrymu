@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('transactions.index') }}" class="flex items-center text-gray-500 hover:text-blue-600 mb-4 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Kembali ke Daftar
    </a>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
            <h2 class="font-bold text-xl text-blue-800">Edit Transaksi #{{ $transaction->id }}</h2>
            <span class="text-xs font-mono text-gray-500">Dibuat: {{ $transaction->created_at->format('d M Y') }}</span>
        </div>
        
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal & Waktu Transaksi</label>
                <input type="datetime-local" name="created_at" 
                    value="{{ $transaction->created_at->format('Y-m-d\TH:i') }}"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                <p class="text-xs text-gray-500 mt-1">Ubah tanggal ini jika Anda lupa input transaksi kemarin (Backdate).</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pengerjaan</label>
                    <select name="status" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="process" {{ $transaction->status == 'process' ? 'selected' : '' }}>Process (Dicuci)</option>
                        <option value="ready" {{ $transaction->status == 'ready' ? 'selected' : '' }}>Ready (Siap Ambil)</option>
                        <option value="taken" {{ $transaction->status == 'taken' ? 'selected' : '' }}>Taken (Sudah Diambil)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="payment_status" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="unpaid" {{ $transaction->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid (Belum Lunas)</option>
                        <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                    </select>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h3 class="font-bold text-yellow-800 mb-3 text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                    </svg>
                    Opsi Pengiriman
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Metode Ambil</label>
                        <select name="delivery_type" id="deliveryType" class="w-full border-yellow-300 rounded-lg p-2.5 focus:ring-yellow-500" onchange="toggleFee()">
                            <option value="pickup" {{ $transaction->delivery_type == 'pickup' ? 'selected' : '' }}>Ambil Sendiri (Di Outlet)</option>
                            <option value="delivery" {{ $transaction->delivery_type == 'delivery' ? 'selected' : '' }}>Diantar Kurir (Delivery)</option>
                        </select>
                    </div>

                    <div id="feeInputBox" class="{{ $transaction->delivery_type == 'delivery' ? '' : 'hidden' }}">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Biaya Ongkir (Rp)</label>
                        <input name="delivery_fee" type="number" value="{{ $transaction->delivery_fee }}" class="w-full border-yellow-300 rounded-lg p-2.5" placeholder="0">
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pelanggan</label>
                    <select name="customer_id" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $transaction->customer_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->is_member ? 'Member' : 'Umum' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berat / Jumlah</label>
                        <input name="weight" type="number" step="0.1" value="{{ $transaction->weight }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                        <p class="text-xs text-gray-500 mt-1">*Mengubah data di bagian ini akan menghitung ulang total harga.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 font-medium px-4 py-2 text-sm">Batal</a>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-sm px-6 py-2.5 transition shadow-lg hover:shadow-xl">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFee() {
        const type = document.getElementById('deliveryType').value;
        const feeBox = document.getElementById('feeInputBox');
        
        if(type === 'delivery') {
            feeBox.classList.remove('hidden');
        } else {
            feeBox.classList.add('hidden');
        }
    }
</script>
@endsection