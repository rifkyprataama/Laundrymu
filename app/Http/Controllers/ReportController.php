<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense; // <--- BARU: Panggil Model Expense
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // ==========================================
        // 1. DATA PEMASUKAN (TRANSAKSI)
        // ==========================================
        $query = Transaction::where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate);

        // Ambil semua data transaksi untuk hitungan total (bukan pagination)
        $allTransactions = $query->clone()->orderBy('paid_at', 'asc')->get();

        $totalIncome = $allTransactions->sum('total_price');
        $totalCash = $allTransactions->where('payment_method', 'cash')->sum('total_price');
        $totalTransfer = $allTransactions->whereIn('payment_method', ['transfer', 'qris'])->sum('total_price');

        // ==========================================
        // 2. DATA PENGELUARAN (EXPENSE) - BARU!
        // ==========================================
        // Ambil data pengeluaran di rentang tanggal yang sama
        $expenses = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->get();
            
        $totalExpense = $expenses->sum('amount'); // Hitung Total Pengeluaran

        // ==========================================
        // 3. HITUNG LABA BERSIH (NET PROFIT) - BARU!
        // ==========================================
        // Rumus: Total Pemasukan - Total Pengeluaran
        $netProfit = $totalIncome - $totalExpense;

        // ==========================================
        // 4. LOGIKA GRAFIK (PEMASUKAN)
        // ==========================================
        $dataGrouped = $allTransactions->groupBy(function($item) {
            return Carbon::parse($item->paid_at)->format('Y-m-d');
        })->map(function ($row) {
            return $row->sum('total_price');
        });

        $period = CarbonPeriod::create($startDate, $endDate);
        $chartLabels = [];
        $chartValues = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M'); 
            $chartValues[] = $dataGrouped[$dateString] ?? 0;
        }

        // ==========================================
        // 5. DATA TABEL (PAGINATION)
        // ==========================================
        $transactions = $query->clone()
            ->latest('paid_at')
            ->paginate(10)
            ->fragment('tabel-rincian');

        return view('reports.index', compact(
            'transactions', 
            'totalIncome', 'totalCash', 'totalTransfer',
            'totalExpense', 'netProfit', // <--- Variabel BARU dikirim ke View
            'startDate', 'endDate',
            'chartLabels', 'chartValues'
        ));
    }

    // METHOD EXPORT (Tidak ada perubahan, tetap export transaksi)
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = Transaction::where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->oldest('paid_at')
            ->get();

        $filename = 'Laporan-Keuangan-' . $startDate . '-sd-' . $endDate . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Tanggal Bayar', 'Customer', 'Layanan', 'Metode', 'Total (Rp)']);

            foreach ($transactions as $index => $t) {
                fputcsv($handle, [
                    $index + 1,
                    Carbon::parse($t->paid_at)->format('d/m/Y H:i'),
                    $t->customer->name,
                    $t->service->name,
                    strtoupper($t->payment_method),
                    $t->total_price
                ]);
            }
            fclose($handle);
        }, $filename);
    }
}