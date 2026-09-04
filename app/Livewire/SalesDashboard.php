<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SalesDashboard extends Component
{
    public array $cart = [];
    public string $customerName = '';
    public string $paymentMethod = 'cash';
    public ?int $amountTendered = null;
    public bool $showReceipt = false;
    public ?Sale $lastSale = null;

    public function addToCart(int $productId)
    {
        $product = Product::findOrFail($productId);
        
        $found = false;
        foreach ($this->cart as &$item) {
            if ($item['id'] == $product->id) {
                $item['quantity']++;
                $item['subtotal'] = $item['quantity'] * $item['price'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price
            ];
        }
    }

    public function removeFromCart(int $index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        $totalAmount = collect($this->cart)->sum('subtotal');

        if ($this->paymentMethod === 'cash') {
            if (empty($this->amountTendered) || $this->amountTendered < $totalAmount) {
                $this->addError('amountTendered', 'Jumlah uang tunai tidak boleh kurang dari total.');
                return;
            }
            $changeAmount = $this->amountTendered - $totalAmount;
        } else {
            $this->amountTendered = null;
            $changeAmount = null;
        }

        DB::transaction(function () use ($totalAmount, $changeAmount) {
            $sale = Sale::create([
                'total_amount' => $totalAmount,
                'customer_name' => $this->customerName,
                'payment_method' => $this->paymentMethod,
                'amount_tendered' => $this->amountTendered,
                'change_amount' => $changeAmount,
            ]);

            foreach ($this->cart as $item) {
                $product = Product::with('rawMaterials')->find($item['id']);
                
                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal']
                ]);

                // Deduct Raw Materials based on quantity sold and record mutation
                foreach ($product->rawMaterials as $material) {
                    $qtyNeeded = $material->pivot->quantity * $item['quantity'];
                    $material->decrement('stock', $qtyNeeded);

                    \App\Models\StockMutation::create([
                        'raw_material_id' => $material->id,
                        'type' => 'out',
                        'quantity' => $qtyNeeded,
                        'description' => 'PJLB-' . str_pad($sale->id, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            }

            $this->lastSale = $sale->load('items');
        });

        $this->showReceipt = true;
    }

    public function newOrder()
    {
        $this->cart = [];
        $this->customerName = '';
        $this->paymentMethod = 'cash';
        $this->amountTendered = null;
        $this->showReceipt = false;
        $this->lastSale = null;
    }

    public function render()
    {
        $products = Product::where('is_active', true)->get();
        return view('livewire.sales-dashboard', compact('products'));
    }
}
