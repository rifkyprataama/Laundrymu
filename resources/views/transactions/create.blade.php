@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('dashboard') }}" class="flex items-center text-gray-500 hover:text-blue-600 mb-4 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Kembali ke Dashboard
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                <h2 class="font-bold text-xl text-blue-800">Transaksi Baru</h2>
            </div>
            
            <form action="{{ route('transactions.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700">Pilih Pelanggan <span class="text-red-500">*</span></label>
                        <a href="{{ route('customers.create', ['from' => 'transaction']) }}" class="text-xs text-blue-600 font-bold hover:underline bg-blue-50 px-2 py-1 rounded">
                            + Pelanggan Baru
                        </a>
                    </div>
                    <select name="customer_id" required class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Cari Nama Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                {{ (old('customer_id') == $c->id || session('new_customer_id') == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->is_member ? '⭐ Member' : 'Umum' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Layanan <span class="text-red-500">*</span></label>
                        <select name="service_id" required class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} — Rp {{ number_format($s->price) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berat / Jumlah <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input name="weight" type="number" step="0.1" required class="rounded-none rounded-l-lg bg-gray-50 border border-gray-300 w-full p-2.5" placeholder="0.0">
                            <span class="inline-flex items-center px-3 text-sm bg-gray-200 border border-l-0 border-gray-300 rounded-r-md font-bold">Kg/Pcs</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3 text-sm">Opsi Pengiriman</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <select name="delivery_type" id="deliveryType" class="w-full border-gray-300 rounded-lg p-2.5" onchange="toggleFee()">
                                <option value="pickup">Ambil Sendiri (Di Outlet)</option>
                                <option value="delivery">Diantar Kurir (Delivery)</option>
                            </select>
                        </div>
                        <div id="feeInputBox" class="hidden">
                            <input name="delivery_fee" type="number" value="0" class="w-full border-gray-300 rounded-lg p-2.5" placeholder="Biaya Ongkir (Rp)">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Khusus (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-yellow-50 border border-yellow-300 rounded-lg p-2.5 placeholder-gray-400" placeholder="Contoh: Pisahkan baju putih, Noda di kerah, Jangan disetrika..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-sm px-6 py-3 transition shadow-lg">
                        Simpan Transaksi
                    </button>
                </div>
        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Status Pembayaran</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="togglePaymentMethod(false)">
                        <input type="radio" name="payment_status" value="unpaid" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-700">Belum Lunas (Unpaid)</span>
                            <span class="block text-xs text-gray-500">Bayar nanti saat diambil</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border border-green-200 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100 transition" onclick="togglePaymentMethod(true)">
                        <input type="radio" name="payment_status" value="paid" class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-green-700">Lunas Sekarang (Prepaid)</span>
                            <span class="block text-xs text-green-600">Pelanggan bayar di muka</span>
                        </div>
                    </label>

                    <div id="paymentMethodBox" class="hidden mt-3 pl-2 border-l-2 border-green-300">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full text-sm border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Pilih --</option>
                            <option value="cash">💵 Tunai (Cash)</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                            <option value="qris">📱 QRIS / E-Wallet</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Waktu & Deadline</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Transaksi</label>
                        <input type="datetime-local" name="created_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full text-sm border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estimasi Selesai (Janji)</label>
                        <input type="datetime-local" name="deadline" value="{{ now()->addDays(2)->format('Y-m-d\TH:i') }}" class="w-full text-sm border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            </form> </div>
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

    function togglePaymentMethod(isPaid) {
        const box = document.getElementById('paymentMethodBox');
        if (isPaid) {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
            document.querySelector('select[name="payment_method"]').value = "";
        }
    }
</script>
@endsection