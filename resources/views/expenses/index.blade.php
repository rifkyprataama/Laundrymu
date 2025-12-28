@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengeluaran Operasional</h1>
            <p class="text-sm text-gray-500">Catat biaya listrik, air, gaji, dan belanja kebutuhan laundry.</p>
        </div>
        
        <button onclick="openModal()" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Catat Pengeluaran
        </button>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $e)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded text-xs font-bold border border-gray-200">
                                    {{ $e->expense_date->format('d M') }}
                                </span>
                                <span class="text-gray-500 text-xs">{{ $e->expense_date->format('Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $e->description }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-red-600">
                            - Rp {{ number_format($e->amount) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form id="delete-form-{{ $e->id }}" action="{{ route('expenses.destroy', $e->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $e->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition border border-red-100" title="Hapus Data">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-sm">Belum ada pengeluaran tercatat.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $expenses->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

<div id="modalOverlay" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity opacity-0"></div>

<div id="addExpenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full scale-95 opacity-0" id="modalPanel">
            
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="p-1.5 bg-red-100 text-red-600 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    Catat Pengeluaran Baru
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="px-6 py-6 space-y-5">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal Pengeluaran</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700 
                            placeholder-gray-400 bg-white
                            focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 
                            transition duration-200 ease-in-out">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Keterangan / Keperluan</label>
                        <input type="text" name="description" placeholder="Contoh: Beli Deterjen 5 Liter, Bayar Listrik..." 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700 
                            placeholder-gray-400 bg-white
                            focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 
                            transition duration-200 ease-in-out" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nominal (Rupiah)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">Rp</span>
                            </div>
                            <input type="number" name="amount" placeholder="0" 
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-gray-700 font-bold 
                                placeholder-gray-400 bg-white
                                focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 
                                transition duration-200 ease-in-out" required>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- 1. LOGIC MODAL DENGAN ANIMASI ---
    const modal = document.getElementById('addExpenseModal');
    const overlay = document.getElementById('modalOverlay');
    const panel = document.getElementById('modalPanel');

    function openModal() {
        modal.classList.remove('hidden');
        overlay.classList.remove('hidden');
        
        // Animasi Masuk (Perlu sedikit delay agar transisi CSS jalan)
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        // Animasi Keluar
        overlay.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        // Tunggu animasi selesai baru hidden
        setTimeout(() => {
            modal.classList.add('hidden');
            overlay.classList.add('hidden');
        }, 300);
    }

    // Tutup modal jika klik overlay (background gelap)
    overlay.addEventListener('click', closeModal);


    // --- 2. LOGIC SWEETALERT DELETE ---
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Warna Merah Tailwind (red-600)
            cancelButtonColor: '#6b7280', // Warna Abu Tailwind (gray-500)
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true, // Tombol Batal di kiri, Hapus di kanan
            backdrop: `rgba(0,0,0,0.4)`
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

@endsection