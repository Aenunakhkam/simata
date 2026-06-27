<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $academicYearId = session('academic_year_id');

        // 1. Total Saldo Bank Mini (Total Tabungan Nasabah)
        $totalKas = \App\Models\Student::sum('balance');

        // 2. Setoran (Pemasukan) Bulan Ini
        $incomeThisMonth = \App\Models\Transaction::where('type', 'deposit')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // 3. Penarikan (Pengeluaran) Bulan Ini
        $expenseThisMonth = \App\Models\Transaction::where('type', 'withdrawal')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // 4. Total Nasabah Aktif
        $totalStudents = \App\Models\Student::count();

        // 5. Total Tunggakan (Not used in Bank Mini, returning 0 to prevent UI breaking if still there)
        $totalTunggakan = 0;

        // Ambil transaksi terakhir 
        $recentPayments = \App\Models\Transaction::with(['student'])->latest()->take(5)->get();

        $year = request('year', now()->year);

        $incomeData = array_fill(1, 12, 0);
        $expenseData = array_fill(1, 12, 0);

        // Chart Setoran
        $deposits = \App\Models\Transaction::where('type', 'deposit')
            ->whereYear('created_at', $year)
            ->get(['created_at', 'amount']);
        foreach ($deposits as $trx) {
            $month = (int) $trx->created_at->format('m');
            $incomeData[$month] += $trx->amount;
        }

        // Chart Penarikan
        $withdrawals = \App\Models\Transaction::where('type', 'withdrawal')
            ->whereYear('created_at', $year)
            ->get(['created_at', 'amount']);
        foreach ($withdrawals as $trx) {
            $month = (int) $trx->created_at->format('m');
            $expenseData[$month] += $trx->amount;
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_kas' => (float) $totalKas,
                'income_this_month' => (float) $incomeThisMonth,
                'expense_this_month' => (float) $expenseThisMonth,
                'total_tunggakan' => (float) $totalTunggakan,
                'total_students' => $totalStudents,
            ],
            'recent_transactions' => $recentPayments,
            'chartData' => [
                'income' => array_values($incomeData),
                'expense' => array_values($expenseData),
                'selected_year' => (int) $year
            ]
        ]);
    }
}
