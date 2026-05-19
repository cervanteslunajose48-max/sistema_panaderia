<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    // Check Status & Redirect
    public function checkStatus()
    {
        $activeRegister = CashRegister::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if ($activeRegister) {
            return redirect()->route('cash-registers.show', $activeRegister->id);
        }

        return redirect()->route('cash-registers.create');
    }

    // Show Open Form
    public function create()
    {
        // Prevent opening if already open
        $activeRegister = CashRegister::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if ($activeRegister) {
            return redirect()->route('cash-registers.show', $activeRegister->id);
        }

        return view('cash_registers.create');
    }

    // Process Opening
    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $register = CashRegister::create([
            'user_id' => Auth::id(),
            'status' => 'open',
            'opening_amount' => $request->opening_amount,
            'opening_time' => Carbon::now(),
        ]);

        return redirect()->route('pos.index')->with('success', 'Caja abierta correctamente.');
    }

    // Dashboard (Show Balance)
    public function show(CashRegister $cashRegister)
    {
        // Security: Only owner or admin
        if ($cashRegister->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $cashRegister->load([
            'movements' => function ($query) {
                $query->latest();
            }
        ]);

        // Calculate Balance logic (simplified)
        // Opening + Sales (Orders) + In - Out
        // Note: For now we track Sales directly via Orders linked to User or if we link Order to Register later
        // Strategy: Get Orders created by this user since opening_time

        $salesTotal = \App\Models\Order::where('user_id', $cashRegister->user_id)
            ->where('created_at', '>=', $cashRegister->opening_time)
            ->where('status', 'completed')
            ->sum('total');

        $movementsIn = $cashRegister->movements()->where('type', 'in')->sum('amount');
        $movementsOut = $cashRegister->movements()->where('type', 'out')->sum('amount');

        $currentBalance = $cashRegister->opening_amount + $salesTotal + $movementsIn - $movementsOut;

        return view('cash_registers.show', compact('cashRegister', 'salesTotal', 'currentBalance'));
    }

    // Process Close
    public function close(Request $request, CashRegister $cashRegister)
    {
        // Recalculate finals
        $salesTotal = \App\Models\Order::where('user_id', $cashRegister->user_id)
            ->where('created_at', '>=', $cashRegister->opening_time)
            ->where('status', 'completed')
            ->sum('total');

        $movementsIn = $cashRegister->movements()->where('type', 'in')->sum('amount');
        $movementsOut = $cashRegister->movements()->where('type', 'out')->sum('amount');
        $calculatedSystemAmount = $cashRegister->opening_amount + $salesTotal + $movementsIn - $movementsOut;

        $cashRegister->update([
            'status' => 'closed',
            'closing_amount' => $request->actual_cash ?? 0, // In real app, user inputs this
            'closing_time' => Carbon::now(),
            'notes' => "Sistema calculó: $calculatedSystemAmount",
        ]);

        return redirect()->route('dashboard')->with('success', 'Caja cerrada. Resumen generado.');
    }

    // Store Movement (Expense/Deposit)
    public function storeMovement(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
        ]);

        $cashRegister->movements()->create($request->all());

        return redirect()->back()->with('success', 'Movimiento registrado.');
    }
}
