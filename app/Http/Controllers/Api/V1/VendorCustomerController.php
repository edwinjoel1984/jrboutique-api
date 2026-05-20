<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\VendorCustomer;
use Illuminate\Http\Request;

class VendorCustomerController extends Controller
{
    public function byVendor($vendor_id, Request $request)
    {
        $user = $request->user();
        // Vendors can only see their own customers
        if ($user->role_id == 2 && $user->id != $vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $customers = VendorCustomer::with('customer')
            ->where('vendor_id', $vendor_id)
            ->get()
            ->map(fn($vc) => $vc->customer);

        return response()->json(['body' => $customers]);
    }

    public function eligible($vendor_id, Request $request)
    {
        $user = $request->user();
        if ($user->role_id == 2 && $user->id != $vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $assignedIds = VendorCustomer::where('vendor_id', $vendor_id)->pluck('customer_id');
        $eligible = Customer::whereNotIn('id', $assignedIds)->orderBy('first_name')->get();

        return response()->json(['body' => $eligible]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id'   => 'required|integer|exists:users,id',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        $user = $request->user();
        if ($user->role_id == 2 && $user->id != $data['vendor_id']) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $exists = VendorCustomer::where('vendor_id', $data['vendor_id'])
            ->where('customer_id', $data['customer_id'])
            ->exists();

        if ($exists) {
            return response()->json(['body' => 'El cliente ya está asignado a este vendedor'], 422);
        }

        $vc = VendorCustomer::create($data);
        return response()->json(['body' => $vc], 201);
    }

    public function destroy($id, Request $request)
    {
        $vc = VendorCustomer::findOrFail($id);
        $user = $request->user();

        if ($user->role_id == 2 && $user->id != $vc->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $vc->delete();
        return response()->json(['body' => 'Asignación eliminada']);
    }

    public function withBalance($vendor_id, Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $customers = Customer::join('vendor_customers', 'vendor_customers.customer_id', '=', 'customers.id')
            ->where('vendor_customers.vendor_id', $vendor_id)
            ->leftJoin('vendor_assignments', function ($join) use ($vendor_id) {
                $join->on('vendor_assignments.customer_id', '=', 'customers.id')
                    ->where('vendor_assignments.vendor_id', '=', $vendor_id);
            })
            ->leftJoin('vendor_customer_payments', function ($join) use ($vendor_id) {
                $join->on('vendor_customer_payments.customer_id', '=', 'customers.id')
                    ->where('vendor_customer_payments.vendor_id', '=', $vendor_id);
            })
            ->selectRaw('customers.id, customers.first_name, customers.last_name, customers.document, customers.phone,
                COALESCE(SUM(vendor_assignments.quantity * vendor_assignments.unit_price), 0) as total_assigned,
                COALESCE(SUM(vendor_customer_payments.amount), 0) as total_paid')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.document', 'customers.phone')
            ->get()
            ->map(function ($c) {
                $c->balance = (float) $c->total_assigned - (float) $c->total_paid;
                $c->total_assigned = (float) $c->total_assigned;
                $c->total_paid = (float) $c->total_paid;
                return $c;
            });

        return response()->json(['body' => $customers]);
    }
}
