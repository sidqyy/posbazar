<div>
    <!-- Header & Filter Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Laporan Penjualan
                <span class="text-sm font-normal text-pink-600 bg-pink-50 border border-pink-200 px-3 py-1 rounded-full ml-2">
                    @if($filterPeriod === 'all')
                        Semua Waktu
                    @elseif($filterPeriod === 'today')
                        Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d F Y') }})
                    @elseif($filterPeriod === 'month')
                        Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})
                    @elseif($filterPeriod === 'custom')
                        Custom ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                    @endif
                </span>
            </h2>
            <p class="text-gray-500 text-sm mt-1">Ringkasan transaksi penjualan, buket terjual, dan mutasi stok bahan baku.</p>
        </div>

        <!-- Filter Period Buttons -->
        <div class="flex flex-wrap items-center gap-2 bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm">
            <button wire:click="setPeriod('all')" class="px-3.5 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filterPeriod === 'all' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua Waktu
            </button>
            <button wire:click="setPeriod('today')" class="px-3.5 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filterPeriod === 'today' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                Hari Ini
            </button>
            <button wire:click="setPeriod('month')" class="px-3.5 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filterPeriod === 'month' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                Bulan Ini
            </button>
            <button wire:click="setPeriod('custom')" class="px-3.5 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filterPeriod === 'custom' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                Custom Tanggal
            </button>
        </div>
    </div>

    <!-- Custom Date Inputs (only when Custom Period is active) -->
    @if($filterPeriod === 'custom')
        <div class="bg-pink-50/50 border border-pink-100 p-4 rounded-xl mb-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-600 uppercase">Dari Tanggal:</label>
                <input type="date" wire:model.live="startDate" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-pink-500 focus:outline-none">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-600 uppercase">Sampai Tanggal:</label>
                <input type="date" wire:model.live="endDate" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-pink-500 focus:outline-none">
            </div>
        </div>
    @endif

    <!-- Cards Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
            <h3 class="text-gray-500 font-medium mb-1 text-sm">Total Pendapatan</h3>
            <p class="text-3xl font-bold text-pink-600">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
            <h3 class="text-gray-500 font-medium mb-1 text-sm">Total Transaksi</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalTransactions }} <span class="text-base text-gray-400 font-normal">order</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
            <h3 class="text-gray-500 font-medium mb-1 text-sm">Rata-rata Transaksi</h3>
            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Product Summary & Critical Stock Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Buket / Produk Terjual -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Buket Terjual</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">Total: {{ $soldItems->sum('total_qty') }} pcs</span>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($soldItems->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Tidak ada penjualan pada periode ini.
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold border-b border-gray-100">
                            <tr>
                                <th class="p-3">Nama Produk</th>
                                <th class="p-3 text-center">Jumlah</th>
                                <th class="p-3 text-right">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($soldItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-800">{{ $item->product_name }}</td>
                                    <td class="p-4 text-center font-bold text-pink-600">{{ $item->total_qty }}x</td>
                                    <td class="p-4 text-right text-gray-600 font-medium">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Warning Stok Bahan -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-red-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Peringatan Stok Tipis (< 10)
                </h3>
                <span class="text-xs text-red-500 bg-red-50 px-2.5 py-1 rounded-full font-medium border border-red-100">{{ $criticalMaterials->count() }} Bahan</span>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                @if($criticalMaterials->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Semua stok bahan baku aman.
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($criticalMaterials as $material)
                                <tr class="bg-red-50/60 hover:bg-red-100/60 transition-colors">
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

    <!-- Detailed Records (Transactions & Mutations) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Riwayat Pesanan / Transaksi -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Transaksi</h3>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama/metode/kode..." class="w-full sm:w-60 bg-white border border-gray-200 text-xs rounded-lg px-3 py-1.5 pl-8 focus:ring-2 focus:ring-pink-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($recentSales->isEmpty())
                    <div class="p-8 text-center text-gray-500">Tidak ada transaksi ditemukan.</div>
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
                                            <div class="font-medium text-gray-800">
                                                @if($filterPeriod === 'today')
                                                    {{ $sale->created_at->format('H:i') }}
                                                @else
                                                    {{ $sale->created_at->format('d M Y') }}
                                                    <span class="text-xs text-gray-500 block">{{ $sale->created_at->format('H:i') }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-mono text-pink-600 mt-1">PJLB-{{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="p-3 align-top">
                                            @if($sale->customer_name)
                                                <div class="font-medium text-gray-800 mb-1">Pelanggan: {{ $sale->customer_name }}</div>
                                            @endif
                                            <ul class="text-xs text-gray-600 space-y-2 mt-1">
                                                @foreach($sale->items as $item)
                                                    <li>
                                                        <span class="font-semibold text-gray-700">• {{ $item->quantity }}x {{ $item->product_name }}</span>
                                                        @if($item->product && $item->product->rawMaterials->count() > 0)
                                                            <div class="ml-3 mt-1 text-gray-500 border-l-2 border-pink-200 pl-2">
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
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Mutasi Stok</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">{{ $recentMutations->count() }} Catatan</span>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($recentMutations->isEmpty())
                    <div class="p-8 text-center text-gray-500">Belum ada mutasi stok pada periode ini.</div>
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
                                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap">
                                            @if($filterPeriod === 'today')
                                                {{ $mutation->created_at->format('H:i:s') }}
                                            @else
                                                {{ $mutation->created_at->format('d M Y') }}
                                                <span class="text-[10px] text-gray-400 block">{{ $mutation->created_at->format('H:i:s') }}</span>
                                            @endif
                                        </td>
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

