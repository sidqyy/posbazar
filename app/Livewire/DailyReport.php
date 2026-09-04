<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\RawMaterial;
use Carbon\Carbon;

class DailyReport extends Component
{
    public function render()
    {
        $today = Carbon::today();
        
        $totalSalesAmount = Sale::whereDate('created_at', $today)->sum('total_amount');
        $totalTransactions = Sale::whereDate('created_at', $today)->count();
        
        $soldItems = SaleItem::with('product')
            ->whereDate('created_at', $today)
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->get();
            
        $criticalMaterials = RawMaterial::where('stock', '<', 10)->get();

        $recentSales = Sale::with('items.product.rawMaterials')
            ->whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->get();

        $recentMutations = \App\Models\StockMutation::with('rawMaterial')
            ->whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.daily-report', compact(
            'totalSalesAmount', 
            'totalTransactions', 
            'soldItems',
            'criticalMaterials',
            'recentSales',
            'recentMutations'
        ));
    }
}
