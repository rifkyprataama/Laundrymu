@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-green-50 px-6 py-4 border-b border-green-100">
            <h2 class="font-bold text-xl text-green-800">Tambah Pelanggan Baru</h2>
        </div>
        
        <form action="{{ route('customers.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                <input name="name" type="text" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5" placeholder="Contoh: Rina Nose" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                <input name="phone" type="number" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5" placeholder="0812..." required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat</label>
                <textarea name="address" rows="3" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5" placeholder="Alamat lengkap..." required></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="{{ route('transactions.create') }}" class="text-gray-500 hover:text-gray-700 font-medium px-4 py-2 text-sm">Batal</a>
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-bold rounded-lg text-sm px-6 py-2.5 transition">
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection