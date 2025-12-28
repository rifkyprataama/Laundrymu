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
                            
                            <div class="text-xs text-gray-500 mb-1">
                                {{ $t->customer->phone }}
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
                                    
                                    <a href="{{ route('transactions.show', $t->id) }}" class="text-gray-600 hover:text-blue-600 bg-gray-50 border border-gray-200 hover:border-blue-300 p-1.5 rounded transition" title="Print Struk / Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>

                                    @php
                                        $msg = "Halo Kak {$t->customer->name}, Laundry (Nota #{$t->id}) ";
                                        if($t->status == 'done') {
                                            $msg .= "sudah SELESAI dan SIAP DIAMBIL. ";
                                        } elseif($t->status == 'process') {
                                            $msg .= "sedang kami PROSES. ";
                                        } else {
                                            $msg .= "sudah kami TERIMA. ";
                                        }
                                        $msg .= "Total Tagihan: Rp " . number_format($t->total_price);

                                        $waLink = "https://wa.me/" . $t->customer->whatsapp_url . "?text=" . urlencode($msg);
                                    @endphp

                                    <a href="{{ $waLink }}" target="_blank" class="text-green-600 hover:text-green-700 bg-green-50 border border-green-200 hover:border-green-300 p-1.5 rounded transition" title="Kirim Info ke WA">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('transactions.edit', $t->id) }}" class="text-gray-600 hover:text-yellow-600 bg-gray-50 border border-gray-200 hover:border-yellow-300 p-1.5 rounded transition" title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                                
                                @if($t->status == 'pending' && $t->payment_status == 'unpaid')
                                    <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="form-delete w-full">
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