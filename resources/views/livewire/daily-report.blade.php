<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Laporan Penjualan Hari Ini</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
            <h3 class="text-gray-500 font-medium mb-2">Total Pendapatan</h3>
            <p class="text-4xl font-bold text-pink-600">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
            <h3 class="text-gray-500 font-medium mb-2">Total Transaksi</h3>
            <p class="text-4xl font-bold text-gray-800">{{ $totalTransactions }} <span class="text-lg text-gray-400 font-normal">order</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Best Sellers -->
        <div>
            <h3 class="text-lg font-bold mb-4 text-gray-800">Buket Terjual</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($soldItems->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Belum ada penjualan hari ini.
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($soldItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-800">{{ $item->product_name }}</td>
                                    <td class="p-4 text-center font-bold text-pink-600">{{ $item->total_qty }}x</td>
                                    <td class="p-4 text-right text-gray-500">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Critical Stocks -->
        <div>
            <h3 class="text-lg font-bold mb-4 text-red-600 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                Peringatan Stok Tipis
            </h3>
            <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                @if($criticalMaterials->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Semua stok bahan baku aman.
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($criticalMaterials as $material)
                                <tr class="bg-red-50 hover:bg-red-100 transition-colors">
                                    <td class="p-4 font-medium text-gray-800">{{ $material->name }}</td>
                                    <td class="p-4 text-right font-bold text-red-600">{{ $material->stock }} {{ $material->unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Records -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Riwayat Pesanan -->
        <div>
            <h3 class="text-lg font-bold mb-4 text-gray-800">Riwayat Transaksi Hari Ini</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($recentSales->isEmpty())
                    <div class="p-8 text-center text-gray-500">Belum ada transaksi.</div>
                @else
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse text-sm min-w-[500px]">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="p-3 font-semibold">Waktu & Kode</th>
                                    <th class="p-3 font-semibold">Rincian Pembelian</th>
                                    <th class="p-3 font-semibold text-right">Total & Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentSales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 text-gray-600 align-top">
                                            <div class="font-medium text-gray-800">{{ $sale->created_at->format('H:i') }}</div>
                                            <div class="text-xs">PJLB-{{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="p-3 align-top">
                                            @if($sale->customer_name)
                                                <div class="font-medium text-gray-800 mb-1">Pelanggan: {{ $sale->customer_name }}</div>
                                            @endif
                                            <ul class="text-xs text-gray-600 space-y-2 mt-2">
                                                @foreach($sale->items as $item)
                                                    <li>
                                                        <span class="font-semibold text-gray-700">• {{ $item->quantity }}x {{ $item->product_name }}</span>
                                                        @if($item->product && $item->product->rawMaterials->count() > 0)
                                                            <div class="ml-3 mt-1 text-gray-500 border-l-2 border-gray-200 pl-2">
                                                                @foreach($item->product->rawMaterials as $rm)
                                                                    <div>- {{ $rm->pivot->quantity * $item->quantity }} {{ $rm->unit }} {{ $rm->name }}</div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if($sale->payment_method === 'cash')
                                                <div class="mt-2 text-xs border-t border-gray-100 pt-1">
                                                    <div class="flex justify-between text-gray-500">
                                                        <span>Tunai:</span>
                                                        <span>Rp {{ number_format($sale->amount_tendered, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-gray-800 font-medium">
                                                        <span>Kembali:</span>
                                                        <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right align-top">
                                            <div class="font-bold text-pink-600">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500 uppercase mt-1">{{ $sale->payment_method == 'tf' ? 'TF Bank' : $sale->payment_method }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Mutasi Stok -->
        <div>
            <h3 class="text-lg font-bold mb-4 text-gray-800">Riwayat Mutasi Stok</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($recentMutations->isEmpty())
                    <div class="p-8 text-center text-gray-500">Belum ada mutasi stok.</div>
                @else
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="p-3 font-semibold">Waktu</th>
                                    <th class="p-3 font-semibold">Bahan Baku</th>
                                    <th class="p-3 font-semibold">Mutasi</th>
                                    <th class="p-3 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentMutations as $mutation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 text-gray-500 whitespace-nowrap">{{ $mutation->created_at->format('H:i:s') }}</td>
                                        <td class="p-3 font-medium text-gray-800">{{ $mutation->rawMaterial->name ?? 'Dihapus' }}</td>
                                        <td class="p-3 font-bold {{ $mutation->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $mutation->type == 'in' ? '+' : '-' }}{{ $mutation->quantity }}
                                        </td>
                                        <td class="p-3 text-gray-500 text-xs">{{ $mutation->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
