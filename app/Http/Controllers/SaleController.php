<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sales = Sale::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('dashboard.salesperson.leads.index', compact('sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'amount' => 'required|numeric',
            'product' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $sale = Sale::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'amount' => $request->amount,
            'product' => $request->product,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sale recorded successfully',
            'sale' => $sale
        ]);
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        return view('dashboard.salesperson.sales.show', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorize('update', $sale);
        
        $request->validate([
            'customer_name' => 'required|string',
            'amount' => 'required|numeric',
            'product' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $sale->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sale updated successfully',
            'sale' => $sale
        ]);
    }

    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);
        
        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully'
        ]);
    }
} 