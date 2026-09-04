<div class="relative min-h-[calc(100vh-8rem)]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Kasir Bazar</h2>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Product List -->
        <div class="lg:col-span-8">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 cursor-pointer hover:border-pink-400 hover:shadow-md hover:bg-pink-50 transition-all flex flex-col justify-between h-full active:scale-95">
                        <h3 class="font-bold text-gray-800 text-sm leading-tight mb-2">{{ $product->name }}</h3>
                        <p class="text-pink-600 font-black">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Section -->
        <div class="lg:col-span-4 lg:sticky lg:top-6 mt-8 lg:mt-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col lg:h-[calc(100vh-8rem)]">
                <h2 class="text-xl font-black mb-4 text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Pesanan Saat Ini
                </h2>

                <!-- Customer Name -->
                <div class="mb-4">
                    <input type="text" wire:model="customerName" placeholder="Nama Pelanggan (Opsional)" class="w-full text-sm border-gray-200 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2">
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto mb-4 pr-2 space-y-3 custom-scrollbar">
                    @forelse($cart as $index => $item)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="flex-1">
                                <h4 class="font-bold text-sm text-gray-800">{{ $item['name'] }}</h4>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-sm text-pink-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                <button wire:click="removeFromCart({{ $index }})" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <p class="text-sm">Belum ada pesanan</p>
                        </div>
                    @endforelse
                </div>

                <!-- Metode Pembayaran Input -->
                <div class="mb-4 shrink-0">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Metode Bayar</label>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="cash" class="peer sr-only">
                            <div class="text-center px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white text-gray-600 peer-checked:bg-pink-50 peer-checked:border-pink-500 peer-checked:text-pink-600 font-medium transition-colors">
                                Cash
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="qris" class="peer sr-only">
                            <div class="text-center px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white text-gray-600 peer-checked:bg-pink-50 peer-checked:border-pink-500 peer-checked:text-pink-600 font-medium transition-colors">
                                QRIS
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="tf" class="peer sr-only">
                            <div class="text-center px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white text-gray-600 peer-checked:bg-pink-50 peer-checked:border-pink-500 peer-checked:text-pink-600 font-medium transition-colors">
                                TF Bank
                            </div>
                        </label>
                    </div>
                </div>

                @if($paymentMethod === 'cash')
                    <!-- Jumlah Uang Diterima -->
                    <div class="mb-4 shrink-0">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah Uang Diterima (Rp)</label>
                        <input type="number" wire:model="amountTendered" placeholder="Contoh: 100000" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-white border p-2 @error('amountTendered') border-red-500 @enderror">
                        @error('amountTendered') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4 pt-2 border-t border-gray-100 shrink-0">
                    <span class="text-gray-600 font-medium">Total:</span>
                    <span class="text-2xl font-bold text-gray-900">Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                <button 
                    wire:click="checkout" 
                    @if(empty($cart)) disabled @endif
                    class="w-full shrink-0 bg-pink-500 hover:bg-pink-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors"
                >
                    Bayar & Proses
                </button>
            </div>
        </div>
    </div>

    <!-- Receipt / Nota Modal -->
    @if($showReceipt && $lastSale)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Struk Content -->
            <div class="p-6 overflow-y-auto bg-[#fdfdfd] border-b-2 border-dashed border-gray-200" id="receipt-content">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-wider">BAZAR POS</h2>
                    <p class="text-xs text-gray-500 mt-1">Struk Pembelian Buket</p>
                </div>
                
                <div class="border-y border-dashed border-gray-300 py-3 mb-4 text-xs font-mono text-gray-600 flex justify-between">
                    <div>
                        <p>PJLB-{{ str_pad($lastSale->id, 3, '0', STR_PAD_LEFT) }}</p>
                        <p>{{ $lastSale->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right font-bold text-gray-800">
                        @if($lastSale->customer_name)
                            <p>Pelanggan: {{ $lastSale->customer_name }}</p>
                        @endif
                        <p class="uppercase text-pink-600 mt-1">
                            {{ $lastSale->payment_method == 'tf' ? 'TF Bank' : $lastSale->payment_method }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    @foreach($lastSale->items as $item)
                        <div class="flex justify-between text-sm">
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="font-bold text-gray-800">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t-2 border-gray-800 pt-3 flex justify-between items-center mb-2">
                    <span class="font-bold text-lg text-gray-800">TOTAL</span>
                    <span class="font-black text-xl text-gray-900">Rp {{ number_format($lastSale->total_amount, 0, ',', '.') }}</span>
                </div>

                @if($lastSale->payment_method === 'cash')
                <div class="flex justify-between items-center text-sm mb-1 text-gray-600">
                    <span>TUNAI</span>
                    <span>Rp {{ number_format($lastSale->amount_tendered, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm font-bold text-gray-800">
                    <span>KEMBALI</span>
                    <span>Rp {{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="text-center text-xs text-gray-500 mt-8 font-medium">
                    <p>Terima kasih atas pesanannya!</p>
                    <p>Silakan foto struk ini sebagai bukti pembayaran.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-4 bg-gray-50 flex gap-3">
                <button wire:click="newOrder" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors">
                    Pesanan Baru (Tutup)
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
