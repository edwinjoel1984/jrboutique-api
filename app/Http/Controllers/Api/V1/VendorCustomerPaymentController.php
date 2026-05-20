<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VendorAssignment;
use App\Models\VendorCustomerPayment;
use App\Models\VendorPeriod;
use Illuminate\Http\Request;

class VendorCustomerPaymentController extends Controller
{
    public function byPeriod($period_id, Request $request)
    {
        $user = $request->user();
        $period = VendorPeriod::findOrFail($period_id);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $payments = VendorCustomerPayment::with('customer')
            ->where('period_id', $period_id)
            ->orderBy('date')
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'customer_id' => $p->customer_id,
                'customer'    => $p->customer ? [
                    'id'         => $p->customer->id,
                    'first_name' => $p->customer->first_name,
                    'last_name'  => $p->customer->last_name,
                ] : null,
                'amount'      => $p->amount,
                'date'        => $p->date,
                'notes'       => $p->notes,
            ]);

        return response()->json(['body' => $payments]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'period_id'   => 'required|integer|exists:vendor_periods,id',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'notes'       => 'nullable|string|max:500',
        ]);

        $period = VendorPeriod::findOrFail($data['period_id']);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        // Calculate customer's pending balance in this period
        $totalAssigned = VendorAssignment::where('period_id', $data['period_id'])
            ->where('customer_id', $data['customer_id'])
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;

        $totalPaid = VendorCustomerPayment::where('period_id', $data['period_id'])
            ->where('customer_id', $data['customer_id'])
            ->sum('amount');

        $pendingBalance = $totalAssigned - $totalPaid;

        if ($data['amount'] > $pendingBalance) {
            return response()->json([
                'body' => "El abono ($data[amount]) supera el saldo pendiente del cliente (" . number_format($pendingBalance, 0) . ")"
            ], 422);
        }

        $payment = VendorCustomerPayment::create([
            'vendor_id'   => $period->vendor_id,
            'customer_id' => $data['customer_id'],
            'period_id'   => $data['period_id'],
            'amount'      => $data['amount'],
            'date'        => $data['date'],
            'notes'       => $data['notes'] ?? null,
        ]);

        return response()->json(['body' => $payment], 201);
    }
}
