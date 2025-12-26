@extends('layouts.app')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Transaksi</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau status, pembayaran, dan pengiriman.</p>
        </div>
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
                                    Chat WA 💬
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
                                    🚚 Delivery
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
                                
                                <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="form-delete w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-[10px] text-red-600 hover:text-red-800 hover:bg-red-50 border border-transparent hover:border-red-100 py-1 rounded transition">
                                        Hapus
                                    </button>
                                </form>
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