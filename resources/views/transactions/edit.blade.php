@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('transactions.index') }}" class="flex items-center text-gray-500 hover:text-blue-600 mb-4 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Kembali ke Riwayat
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex justify-between items-center">
                <h2 class="font-bold text-xl text-indigo-800">✏️ Edit Transaksi #{{ $transaction->id }}</h2>
                <span class="text-xs font-mono text-gray-500 bg-white px-2 py-1 rounded border">
                    Dibuat: {{ $transaction->created_at->format('d M Y') }}
                </span>
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
                                    {{ $s->name }} — Rp {{ number_format($s->price) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Berat / Jumlah</label>
                        <div class="flex">
                            <input name="weight" type="number" step="0.1" value="{{ $transaction->weight }}" class="rounded-none rounded-l-lg bg-gray-50 border border-gray-300 w-full p-2.5">
                            <span class="inline-flex items-center px-3 text-sm bg-gray-200 border border-l-0 border-gray-300 rounded-r-md font-bold">Kg/Pcs</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">*Total harga akan dihitung ulang otomatis.</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3 text-sm">🚚 Opsi Pengiriman</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <select name="delivery_type" id="deliveryType" class="w-full border-gray-300 rounded-lg p-2.5" onchange="toggleFee()">
                                <option value="pickup" {{ $transaction->delivery_type == 'pickup' ? 'selected' : '' }}>Ambil Sendiri</option>
                                <option value="delivery" {{ $transaction->delivery_type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>
                        <div id="feeInputBox" class="{{ $transaction->delivery_type == 'delivery' ? '' : 'hidden' }}">
                            <input name="delivery_fee" type="number" value="{{ $transaction->delivery_fee }}" class="w-full border-gray-300 rounded-lg p-2.5" placeholder="Biaya Ongkir (Rp)">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-bold rounded-lg text-sm px-6 py-3 transition shadow-lg">
                        Simpan Perubahan
                    </button>
                </div>
        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">⚙️ Status Laundry</h3>
                <select name="status" class="w-full bg-blue-50 border border-blue-300 text-blue-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold">
                    <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>⏳ Pending (Masuk)</option>
                    <option value="process" {{ $transaction->status == 'process' ? 'selected' : '' }}>🫧 Sedang Dicuci</option>
                    <option value="ready" {{ $transaction->status == 'ready' ? 'selected' : '' }}>✅ Selesai (Siap Ambil)</option>
                    <option value="taken" {{ $transaction->status == 'taken' ? 'selected' : '' }}>📦 Sudah Diambil</option>
                </select>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">💰 Status Pembayaran</h3>
                
                @if($transaction->payment_status == 'paid')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <div class="flex justify-center mb-2">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-lg font-bold text-green-800">SUDAH LUNAS</h4>
                        <p class="text-sm text-green-700 mt-1">
                            Dibayar via <span class="font-bold uppercase">{{ $transaction->payment_method }}</span><br>
                            pada {{ \Carbon\Carbon::parse($transaction->paid_at)->format('d M Y, H:i') }}
                        </p>
                        
                        <input type="hidden" name="payment_status" value="paid">
                        <input type="hidden" name="payment_method" value="{{ $transaction->payment_method }}">
                        
                        <div class="mt-3 text-xs text-gray-400 border-t border-green-200 pt-2">
                            *Status pembayaran terkunci untuk mencegah kesalahan tagihan ganda.
                        </div>
                    </div>

                @else
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="togglePaymentMethod(false)">
                            <input type="radio" name="payment_status" value="unpaid" checked class="w-4 h-4 text-blue-600">
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-gray-700">Belum Lunas</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border border-green-200 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100 transition" onclick="togglePaymentMethod(true)">
                            <input type="radio" name="payment_status" value="paid" class="w-4 h-4 text-green-600">
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-green-700">Lunas / Sudah Bayar</span>
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
                            <p class="text-xs text-gray-500 mt-1">Wajib dipilih jika status Lunas.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">📅 Tanggal Nota</h3>
                <input type="datetime-local" name="created_at" value="{{ $transaction->created_at->format('Y-m-d\TH:i') }}" class="w-full text-sm border-gray-300 rounded-lg">
                <p class="text-xs text-gray-400 mt-2">Ubah tanggal ini hanya jika Anda lupa input transaksi kemarin (Backdate).</p>
            </div>

            </form>
        </div>
    </div>
</div>

<script>
    // 1. Logic Ongkir
    function toggleFee() {
        const type = document.getElementById('deliveryType').value;
        const feeBox = document.getElementById('feeInputBox');
        if(type === 'delivery') {
            feeBox.classList.remove('hidden');
        } else {
            feeBox.classList.add('hidden');
        }
    }

    // 2. Logic Metode Pembayaran (TAHAP 4)
    function togglePaymentMethod(isPaid) {
        const box = document.getElementById('paymentMethodBox');
        if (isPaid) {
            // Jika diklik Lunas, munculkan dropdown
            box.classList.remove('hidden');
        } else {
            // Jika diklik Belum Lunas, sembunyikan dropdown
            box.classList.add('hidden');
            // Opsional: Reset pilihan ke kosong jika kembali ke unpaid
            // document.querySelector('select[name="payment_method"]').value = "";
        }
    }
</script>
@endsection