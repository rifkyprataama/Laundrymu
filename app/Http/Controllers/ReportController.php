<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // <-- PENTING: Untuk loop tanggal

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // 1. Ambil Data Transaksi (Untuk Tabel & Summary)
        $transactions = Transaction::where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->latest('paid_at')
            ->get();

        // 2. LOGIKA GRAFIK PRO (ZERO FILLING)
        // Langkah A: Ambil data grouping dari DB
        $dataDB = Transaction::selectRaw('DATE(paid_at) as date, SUM(total_price) as total')
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray(); // Hasil: ['2025-01-01' => 50000, '2025-01-03' => 100000] (Tgl 02 hilang)

        // Langkah B: Bikin Loop Tanggal Lengkap
        $period = CarbonPeriod::create($startDate, $endDate);
        $chartLabels = [];
        $chartValues = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            
            // Masukkan Tanggal ke Sumbu X
            $chartLabels[] = $date->format('d M'); // Format: 01 Jan

            // Cek apakah tanggal ini ada duitnya di DB? Kalau gak ada, isi 0
            $chartValues[] = $dataDB[$dateString] ?? 0;
        }

        // 3. Hitung Summary
        $totalIncome = $transactions->sum('total_price');
        $totalCash = $transactions->where('payment_method', 'cash')->sum('total_price');
        $totalTransfer = $transactions->whereIn('payment_method', ['transfer', 'qris'])->sum('total_price');

        return view('reports.index', compact(
            'transactions', 
            'totalIncome', 'totalCash', 'totalTransfer',
            'startDate', 'endDate',
            'chartLabels', 'chartValues'
        ));
    }

    // FUNGSI BARU: DOWNLOAD EXCEL (CSV)
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = Transaction::where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->oldest('paid_at') // Urutkan dari terlama
            ->get();

        // Nama File
        $filename = 'Laporan-Keuangan-' . $startDate . '-sd-' . $endDate . '.csv';

        // Buat Stream Download (Tanpa simpan file di server)
        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            
            // 1. Tulis Judul Kolom (Header Excel)
            fputcsv($handle, ['No', 'Tanggal Bayar', 'Customer', 'Layanan', 'Metode', 'Total (Rp)']);

            // 2. Tulis Isi Data
            foreach ($transactions as $index => $t) {
                fputcsv($handle, [
                    $index + 1,
                    Carbon::parse($t->paid_at)->format('d/m/Y H:i'),
                    $t->customer->name,
                    $t->service->name, // Pastikan relasi service ada
                    strtoupper($t->payment_method),
                    $t->total_price
                ]);
            }

            fclose($handle);
        }, $filename);
    }
}