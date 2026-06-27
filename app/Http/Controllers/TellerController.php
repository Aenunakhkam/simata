<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\CashLedger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TellerController extends Controller
{
    public function index()
    {
        return Inertia::render('Teller/Index');
    }

    public function search(Request $request)
    {
        $accountNumber = $request->account_number;
        
        $customer = Student::where('nisn', $accountNumber)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Nasabah tidak ditemukan'], 404);
        }

        // Return customer data + their recent transactions
        $recentTransactions = Transaction::where('student_id', $customer->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'account_number' => $customer->nisn,
            'type' => $customer->nasabah_type ?? 'Siswa',
            'balance' => $customer->balance,
            'recent_transactions' => $recentTransactions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:1000'
        ]);

        try {
            DB::beginTransaction();

            $customer = Student::findOrFail($request->student_id);
            $amount = $request->amount;
            $type = $request->type;
            
            $balanceBefore = $customer->balance;
            
            if ($type === 'withdrawal' && $balanceBefore < $amount) {
                return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini.');
            }

            $balanceAfter = $type === 'deposit' ? ($balanceBefore + $amount) : ($balanceBefore - $amount);
            
            // Update customer balance
            $customer->balance = $balanceAfter;
            $customer->save();

            // Create Transaction Record
            $transaction = Transaction::create([
                'transaction_number' => 'TRX-' . time() . '-' . rand(1000, 9999),
                'student_id' => $customer->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $type === 'deposit' ? 'Setoran Tabungan' : 'Tarik Tunai'
            ]);

            // Sync with Global Cash Ledger
            CashLedger::create([
                'date' => Carbon::today(),
                'type' => $type === 'deposit' ? 'debit' : 'credit', // Debit = cash in, Credit = cash out
                'amount' => $amount,
                'description' => ($type === 'deposit' ? 'Setoran nasabah: ' : 'Penarikan nasabah: ') . $customer->name,
                'reference_type' => Transaction::class,
                'reference_id' => $transaction->id
            ]);

            DB::commit();

            return back()->with('success', 'Transaksi berhasil diproses! Saldo saat ini: Rp ' . number_format($balanceAfter, 0, ',', '.'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function receipt($id)
    {
        $transaction = Transaction::with('student')->findOrFail($id);
        
        return Inertia::render('Teller/Receipt', [
            'transaction' => $transaction
        ]);
    }
}
