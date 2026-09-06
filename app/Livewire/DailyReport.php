<?php

namespace App\Livewire;

use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMutation;
use Carbon\Carbon;
use Livewire\Component;

class DailyReport extends Component
{
    public string $filterPeriod = 'all';

    public string $startDate = '';

    public string $endDate = '';

    public string $search = '';

    public function mount()
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
    }

    public function setPeriod(string $period)
    {
        $this->filterPeriod = $period;
    }

    public function render()
    {
        $salesQuery = Sale::query();
        $saleItemsQuery = SaleItem::query();
        $mutationsQuery = StockMutation::query();

        if ($this->filterPeriod === 'today') {
            $today = Carbon::today();
            $salesQuery->whereDate('created_at', $today);
            $saleItemsQuery->whereDate('created_at', $today);
            $mutationsQuery->whereDate('created_at', $today);
        } elseif ($this->filterPeriod === 'month') {
            $now = Carbon::now();
            $salesQuery->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month);
            $saleItemsQuery->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month);
            $mutationsQuery->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month);
        } elseif ($this->filterPeriod === 'custom' && ! empty($this->startDate) && ! empty($this->endDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $salesQuery->whereBetween('created_at', [$start, $end]);
            $saleItemsQuery->whereBetween('created_at', [$start, $end]);
            $mutationsQuery->whereBetween('created_at', [$start, $end]);
        }

        $totalSalesAmount = (clone $salesQuery)->sum('total_amount');
        $totalTransactions = (clone $salesQuery)->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSalesAmount / $totalTransactions : 0;

        $soldItems = (clone $saleItemsQuery)
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->get();

        $criticalMaterials = RawMaterial::where('stock', '<', 10)->get();

        if (! empty($this->search)) {
            $searchTerm = '%'.$this->search.'%';
            $salesQuery->where(function ($q) use ($searchTerm) {
                $q->where('customer_name', 'like', $searchTerm)
                    ->orWhere('payment_method', 'like', $searchTerm)
                    ->orWhere('id', 'like', $searchTerm);
            });
        }

        $recentSales = $salesQuery->with('items.product.rawMaterials')
            ->orderByDesc('created_at')
            ->get();

        $recentMutations = $mutationsQuery->with('rawMaterial')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.daily-report', [
            'totalSalesAmount' => $totalSalesAmount,
            'totalTransactions' => $totalTransactions,
            'averageTransaction' => $averageTransaction,
            'soldItems' => $soldItems,
            'criticalMaterials' => $criticalMaterials,
            'recentSales' => $recentSales,
            'recentMutations' => $recentMutations,
            'filterPeriod' => $this->filterPeriod,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
