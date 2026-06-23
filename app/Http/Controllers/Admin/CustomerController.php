<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('email',      'like', '%'.$request->search.'%')
                  ->orWhere('phone_number','like','%'.$request->search.'%');
            });
        }

        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $customers  = $query->latest()->paginate(15)->withQueryString();
        $total      = Customer::count();
        $verified   = Customer::where('is_verified', true)->count();
        $unverified = $total - $verified;
        $today      = Customer::whereDate('created_at', today())->count();

        return view('admin.customers.index',
            compact('customers', 'total', 'verified', 'unverified', 'today'));
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')
                         ->with('success', "Customer {$customer->full_name} deleted.");
    }
}
