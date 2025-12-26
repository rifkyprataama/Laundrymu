@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('customers.index') }}" class="flex items-center text-gray-500 hover:text-blue-600 mb-4 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Kembali ke Data Pelanggan
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                <h2 class="font-bold text-xl text-purple-800">Edit Profil Pelanggan</h2>
                <span class="text-xs text-purple-600 font-mono">ID: #{{ $customer->id }}</span>
            </div>
            
            <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input name="name" type="text" value="{{ $customer->name }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">No. WhatsApp</label>
                        <input name="phone" type="number" value="{{ $customer->phone }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">
                    </div>
                    
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 flex items-center justify-between">
                        <div>
                            <span class="block text-sm font-bold text-yellow-800">Status Member</span>
                            <span class="text-xs text-yellow-600">Diskon 10% aktif</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_member" value="1" class="sr-only peer" {{ $customer->is_member ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Domisili</label>
                    <textarea name="address" rows="2" class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5">{{ $customer->address }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Internal (Admin Only)</label>
                    <textarea name="notes" rows="2" class="w-full bg-red-50 border border-red-200 rounded-lg p-2.5 placeholder-gray-400" placeholder="Cth: Rumah pagar hitam, Orangnya ramah, Sering minta setrika licin...">{{ $customer->notes }}</textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-purple-700 transition shadow-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Statistik Pelanggan</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total Transaksi</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $customer->transactions->count() }} <span class="text-sm text-gray-400 font-normal">kali cuci</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total Pengeluaran</p>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($customer->transactions->sum('total_price'), 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Terakhir Datang</p>
                        <p class="text-sm font-bold text-gray-700">
                            {{ $customer->transactions()->latest()->first()?->created_at->format('d M Y') ?? '- Belum ada -' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-xl shadow border border-red-100 p-5">
                <h3 class="font-bold text-red-800 mb-2">Zona Bahaya</h3>
                <p class="text-xs text-red-600 mb-4">Menghapus data pelanggan mungkin akan mempengaruhi laporan transaksi lama.</p>
                
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Yakin hapus pelanggan ini? Data tidak bisa kembali.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-white border border-red-300 text-red-600 font-bold py-2 rounded hover:bg-red-600 hover:text-white transition">
                        Hapus Pelanggan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection