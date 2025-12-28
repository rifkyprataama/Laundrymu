<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = Transaction::where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate);

        $allData = $query->clone()->orderBy('paid_at', 'asc')->get();

        $totalIncome = $allData->sum('total_price');
        $totalCash = $allData->where('payment_method', 'cash')->sum('total_price');
        $totalTransfer = $allData->whereIn('payment_method', ['transfer', 'qris'])->sum('total_price');

        $dataGrouped = $allData->groupBy(function($item) {
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

        $transactions = $query->clone()
            ->latest('paid_at')
            ->paginate(10)
            ->fragment('tabel-rincian');

        return view('reports.index', compact(
            'transactions', 
            'totalIncome', 'totalCash', 'totalTransfer', 
            'startDate', 'endDate',
            'chartLabels', 'chartValues' 
        ));
    }

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