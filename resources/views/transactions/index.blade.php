@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h1>
            <p class="text-sm text-gray-500">Pantau status, pembayaran, dan pengiriman.</p>
        </div>

        <form action="{{ route('transactions.index') }}" method="GET" class="w-full md:w-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="block w-full md:w-64 p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Cari nama pelanggan...">
                
                @if(request('search'))
                    <a href="{{ route('transactions.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pelanggan & Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Layanan</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Tagihan</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Waktu (WIB)</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 align-top">
                            <div class="font-bold text-gray-900">{{ $t->customer->name }}</div>
                            <div class="flex items-center gap-1 text-xs text-gray-500 mb-1">
                                <span>{{ $t->customer->phone }}</span>
                                <a href="https://wa.me/{{ $t->customer->phone }}?text=Halo Kak {{ $t->customer->name }}, laundry Anda (ID #{{ $t->id }}) statusnya sekarang: {{ strtoupper($t->status) }}. Total: Rp {{ number_format($t->total_price) }}." 
                                    target="_blank" 
                                    class="text-green-600 hover:text-green-800 bg-green-100 px-1 rounded border border-green-200" 
                                    title="Chat WhatsApp">
                                    Chat WA
                                </a>
                            </div>
                            
                            @if($t->customer->address)
                            <div class="flex items-start gap-1">
                                <span class="mt-0.5">📍</span>
                                <span class="text-xs text-gray-500 italic leading-tight">{{ \Illuminate\Support\Str::limit($t->customer->address, 40) }}</span>
                            </div>
                            @endif
                        </td>

                        <td class="px-4 py-4 align-top">
                            <div class="text-sm font-medium text-gray-900">{{ $t->service->name }}</div>
                            <div class="text-xs text-gray-500">{{ $t->weight }} {{ $t->service->unit }}</div>
                            @if($t->delivery_type == 'delivery')
                                <span class="inline-block mt-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">
                                    Delivery
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-4 align-top text-right">
                            <div class="font-bold text-gray-900">Rp {{ number_format($t->total_price, 0, ',', '.') }}</div>
                            
                            @if($t->delivery_fee > 0)
                                <div class="text-[10px] text-gray-400">+Ongkir {{ number_format($t->delivery_fee) }}</div>
                            @endif

                            <div class="mt-1">
                                @if($t->payment_status == 'paid')
                                    <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded border border-green-200">LUNAS</span>
                                @else
                                    <span class="text-[10px] font-bold text-red-700 bg-red-100 px-1.5 py-0.5 rounded border border-red-200">BELUM LUNAS</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-4 align-top text-center text-xs">
                            <div class="text-gray-500">
                                In: <span class="font-medium text-gray-800">{{ $t->created_at->format('d/m H:i') }}</span>
                            </div>
                            @if($t->picked_up_at)
                                <div class="text-green-600 mt-1">
                                    Out: <span class="font-bold">{{ \Carbon\Carbon::parse($t->picked_up_at)->format('d/m H:i') }}</span>
                                </div>
                            @else
                                <div class="text-gray-300 italic mt-1 text-[10px]">- Belum diambil -</div>
                            @endif
                        </td>

                        <td class="px-4 py-4 align-top text-center">
                            @php
                                $statusColor = match($t->status) {
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                    'process' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'ready' => 'bg-green-50 text-green-700 border-green-200',
                                    'taken' => 'bg-gray-50 text-gray-600 border-gray-200',
                                    default => 'bg-red-50 text-red-700'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold border {{ $statusColor }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 align-top text-center">
                            <div class="flex flex-col gap-2 items-center">
                                <div class="flex gap-1">
                                    <a href="{{ route('transactions.show', $t->id) }}" class="text-gray-600 hover:text-blue-600 bg-gray-50 border border-gray-200 hover:border-blue-300 p-1.5 rounded transition" title="Print Struk">
                                        🖨️
                                    </a>
                                    <a href="{{ route('transactions.edit', $t->id) }}" class="text-gray-600 hover:text-yellow-600 bg-gray-50 border border-gray-200 hover:border-yellow-300 p-1.5 rounded transition" title="Edit Data">
                                        ✏️
                                    </a>
                                </div>
                                
                                @if($t->status == 'pending' && $t->payment_status == 'unpaid')
    
                                    <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="form-delete w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-[10px] text-red-600 hover:text-red-800 hover:bg-red-50 border border-transparent hover:border-red-100 py-1 rounded transition">
                                            Hapus
                                        </button>
                                    </form>

                                @else

                                    <button type="button" class="w-full text-[10px] text-gray-400 bg-gray-50 border border-transparent cursor-not-allowed py-1 rounded flex justify-center items-center gap-1" title="Data terkunci karena sudah Lunas atau sedang Dicuci">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                        Terkunci
                                    </button>

                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Belum ada transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $transactions->appends(['search' => request('search')])->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForms = document.querySelectorAll('.form-delete');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Hapus Transaksi?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) this.submit();
                    });
                });
            });
        });
    </script>
@endsection