<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Customer $customer)
    {
        // CRM Data
        $orders = $customer->orders()->latest()->paginate(5);

        $totalSpent = $customer->orders()->sum('total');

        // Top Products Logic
        // We need to join orders -> order_items -> product_variants -> products
        // This can be heavy, let's keep it somewhat optimized
        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.customer_id', $customer->id)
            ->select('products.name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('customers.show', compact('customer', 'orders', 'totalSpent', 'topProducts'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Customer $customer)
    {
        // $customer->delete();
        return back()->with('error', 'La eliminación está deshabilitada. Use la opción Activar/Desactivar.');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->status = !$customer->status;
        $customer->save();
        $status = $customer->status ? 'activado' : 'desactivado';
        return back()->with('success', "Cliente $status correctamente.");
    }
}
