@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>
            <p class="text-sm text-gray-500">Analisa pemasukan bisnis berdasarkan periode waktu.</p>
        </div>
        
        <form action="{{ route('reports.index') }}" method="GET" class="bg-white p-2 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row gap-2">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 pl-2">Dari:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500">Sampai:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-md transition shadow-sm">
                    Filter
                </button>

                <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2 rounded-md transition shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl p-6 text-white shadow-lg">
            <h3 class="text-blue-100 text-sm font-semibold mb-1">TOTAL PEMASUKAN</h3>
            <div class="text-3xl font-bold">Rp {{ number_format($totalIncome) }}</div>
            <div class="text-xs text-blue-100 mt-2 opacity-80">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-gray-500 text-sm font-bold">Tunai (Cash)</h3>
            </div>
            <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalCash) }}</div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-gray-500 text-sm font-bold">Transfer / QRIS</h3>
            </div>
            <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalTransfer) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
        <h3 class="font-bold text-gray-800 mb-4 text-lg">📈 Grafik Pendapatan Harian</h3>
        <div class="relative h-80 w-full">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Rincian Transaksi Masuk</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Tgl Bayar</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3 text-right">Nominal</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            {{ \Carbon\Carbon::parse($t->paid_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-800">
                            {{ $t->customer->name }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $t->payment_method == 'cash' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ strtoupper($t->payment_method) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-gray-800">
                            Rp {{ number_format($t->total_price) }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('transactions.show', $t->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">Lihat</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada pemasukan pada periode tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    
    // Ambil data dari Controller Laravel
    const labels = {!! json_encode($chartLabels) !!};
    const data = {!! json_encode($chartValues) !!};

    const incomeChart = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: labels, 
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data, 
                backgroundColor: 'rgba(59, 130, 246, 0.2)', 
                borderColor: 'rgba(37, 99, 235, 1)', 
                borderWidth: 2,
                tension: 0.3, 
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false 
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection