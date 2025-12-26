@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-6">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-blue-500"></div>

        <div class="px-8 py-6">
            <h2 class="font-bold text-2xl text-gray-800 mb-1">Pelanggan Baru</h2>
            <p class="text-sm text-gray-500 mb-6">Lengkapi data untuk memudahkan pengantaran & promosi.</p>
            
            <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
                @csrf
                
                <input type="hidden" name="from" value="{{ request('from') }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input name="name" type="text" required class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 focus:ring-green-500 focus:border-green-500" placeholder="Contoh: Rina Nose">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                    <input name="phone" type="number" required class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 focus:ring-green-500 focus:border-green-500" placeholder="0812...">
                    <p class="text-xs text-gray-400 mt-1">*Pastikan nomor aktif untuk kirim notifikasi.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Domisili</label>
                        <textarea name="address" rows="2" required class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 focus:ring-green-500 focus:border-green-500" placeholder="Nama Jalan, Blok, Nomor Rumah..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Patokan / Landmark (Untuk Kurir)</label>
                        <input name="landmark" type="text" class="w-full bg-yellow-50 border border-yellow-300 rounded-lg p-2.5 focus:ring-yellow-500 focus:border-yellow-500 placeholder-gray-400" placeholder="Contoh: Pagar hitam, Depan Indomaret, Cat hijau...">
                    </div>
                </div>

                <hr class="border-gray-100 my-2">

                <div class="flex items-center p-4 border border-blue-100 rounded-lg bg-blue-50">
                    <div class="flex items-center h-5">
                        <input id="is_member" name="is_member" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_member" class="font-bold text-blue-900">Langsung Jadi Member?</label>
                        <p class="text-xs text-blue-700">Pelanggan akan otomatis dapat diskon 10% untuk transaksi ini.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ request('from') == 'transaction' ? route('transactions.create') : route('customers.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm transition">
                        &larr; Batal
                    </a>
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold rounded-lg text-sm px-8 py-3 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection