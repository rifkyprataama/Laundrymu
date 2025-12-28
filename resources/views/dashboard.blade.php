@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
        <p class="text-gray-500 mt-1">Pantau kesehatan bisnis laundry Anda hari ini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">    
        <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm transition hover:shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pemasukan Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-gray-800 mt-1">
                        Rp {{ number_format($incomeToday) }}
                    </h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Omset kotor</p>
        </div>

        <div class="bg-white rounded-xl p-6 border border-red-100 shadow-sm transition hover:shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pengeluaran Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-red-600 mt-1">
                        - Rp {{ number_format($expenseToday) }}
                    </h3>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Biaya operasional & belanja</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg relative overflow-hidden transition transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 p-4 opacity-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-green-100 uppercase tracking-wider">Keuntungan Hari Ini</p>
                <h3 class="text-3xl font-extrabold mt-1">
                    Rp {{ number_format($profitToday) }}
                </h3>
                <p class="text-xs text-green-100 opacity-90 mt-2 font-medium">
                    (Pemasukan - Pengeluaran)
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-yellow-100 shadow-sm transition hover:shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Deadline Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-yellow-600 mt-1">
                        {{ $warningCount }} <span class="text-sm text-gray-400 font-normal">Nota</span>
                    </h3>
                </div>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Cucian wajib selesai hari ini!</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-blue-200 text-blue-700 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase">Sedang Dicuci / Setrika</p>
                    <h4 class="text-xl font-black text-blue-900">{{ $processCount }} <span class="text-sm font-medium text-blue-600">Nota</span></h4>
                </div>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-blue-500 font-bold hover:underline">Lihat &rarr;</a>
        </div>

        <div class="bg-green-50 rounded-xl p-4 border border-green-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-green-200 text-green-700 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-green-600 uppercase">Siap Diambil (Ready)</p>
                    <h4 class="text-xl font-black text-green-900">{{ $readyCount }} <span class="text-sm font-medium text-green-600">Nota</span></h4>
                </div>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-green-500 font-bold hover:underline">Lihat &rarr;</a>
        </div>
        
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="font-bold text-gray-800">5 Transaksi Terakhir</h2>
            <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>
        
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3">Pelanggan</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Status Bayar</th>
                    <th class="px-6 py-3">Status Laundry</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $t)
                <tr class="bg-white border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $t->customer->name }}
                        <div class="text-xs text-gray-400">{{ $t->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold">
                        Rp {{ number_format($t->total_price) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($t->payment_status == 'paid')
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">LUNAS</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold">BELUM</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $color = match($t->status) {
                                'pending' => 'yellow',
                                'process' => 'blue',
                                'ready' => 'green',
                                'taken' => 'gray',
                            };
                        @endphp
                        <span class="text-{{ $color }}-600 font-bold text-xs uppercase">{{ $t->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('transactions.show', $t->id) }}" class="text-gray-500 hover:text-blue-600">
                            🔍 Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada transaksi hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection