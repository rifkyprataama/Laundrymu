@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
    <p class="text-gray-500">Statistik performa laundry hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="text-gray-500 text-sm font-medium">Pendapatan Hari Ini</div>
        <div class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($incomeToday) }}</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="text-gray-500 text-sm font-medium">Pendapatan Bulan Ini</div>
        <div class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($incomeMonth) }}</div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="text-gray-500 text-sm font-medium">Cucian Dalam Proses</div>
        <div class="mt-2 text-2xl font-bold text-blue-600">{{ $activeTransactions }} <span class="text-sm font-normal text-gray-400">Pesanan</span></div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="text-gray-500 text-sm font-medium">Total Member</div>
        <div class="mt-2 text-2xl font-bold text-purple-600">{{ $totalMembers }} <span class="text-sm font-normal text-gray-400">Orang</span></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">5 Transaksi Terakhir</h3>
        <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($recentTransactions as $t)
        <div class="px-6 py-3 flex justify-between items-center hover:bg-gray-50">
            <div>
                <p class="text-sm font-bold text-gray-800">{{ $t->customer->name }}</p>
                <p class="text-xs text-gray-500">{{ $t->created_at->diffForHumans() }}</p>
            </div>
            <span class="text-sm font-bold text-gray-600">Rp {{ number_format($t->total_price) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endsection