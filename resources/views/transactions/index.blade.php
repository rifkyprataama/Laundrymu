@extends('layouts.app')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Transaksi</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau semua aktivitas laundry secara real-time.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID & Tgl</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Layanan</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-blue-600">#{{ $t->id }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $t->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                    {{ substr($t->customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $t->customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $t->customer->phone }}</div>
                                </div>
                            </div>
                            @if($t->customer->is_member)
                                <span class="ml-11 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⭐ Member
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $t->service->name }}</div>
                            <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $t->weight }} {{ $t->service->unit }}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">Rp {{ number_format($t->total_price, 0, ',', '.') }}</div>
                            @if($t->discount_amount > 0)
                                <div class="text-xs text-red-500 line-through">Disc: Rp {{ number_format($t->discount_amount, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @php
                                $statusClasses = match($t->status) {
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 ring-yellow-600/20',
                                    'process' => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-600/20',
                                    'ready' => 'bg-green-50 text-green-700 border-green-200 ring-green-600/20',
                                    'taken' => 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-500/10',
                                    default => 'bg-red-50 text-red-700 border-red-200'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('transactions.show', $t->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition">
                                    Detail & Print
                                </a>

                                <a href="{{ route('transactions.edit', $t->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition">
                                    Edit
                                </a>
                                
                                <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="form-delete inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="mx-auto h-12 w-12 text-gray-300">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat transaksi baru.</p>
                            <div class="mt-6">
                                <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    + Buat Transaksi
                                </a>
                            </div>
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
                
                const currentForm = this; 
                
                Swal.fire({
                    title: 'Yakin hapus data ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentForm.submit(); 
                    }
                });
            });
        });
    });
</script>
@endsection