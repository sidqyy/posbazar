<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Katalog Buket</h2>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
    <!-- Form Area -->
    <div class="lg:w-1/3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:sticky lg:top-4">
            <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">
                {{ $editId ? 'Edit Buket' : 'Tambah Buket Baru' }}
            </h3>
            
            <form wire:submit.prevent="saveProduct" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Buket</label>
                    <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                    <input type="number" wire:model="price" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2" required min="0">
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Resep Bahan Baku</label>
                        <button type="button" wire:click="addMaterialRow" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 px-2 py-1 rounded transition-colors">+ Tambah Baris</button>
                    </div>
                    
                    @foreach($selectedMaterials as $index => $mat)
                        <div class="flex gap-2 mb-2 items-start">
                            <div class="flex-1">
                                <select wire:model="selectedMaterials.{{ $index }}.material_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-white border p-2 text-sm">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($rawMaterials as $rm)
                                        <option value="{{ $rm->id }}">{{ $rm->name }} ({{ $rm->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-20">
                                <input type="number" wire:model="selectedMaterials.{{ $index }}.quantity" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2 text-sm" min="1" placeholder="Qty">
                            </div>
                            <button type="button" wire:click="removeMaterialRow({{ $index }})" class="mt-1 text-red-500 hover:text-red-700 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    @endforeach
                    <p class="text-xs text-gray-500 mt-1">Isi resep ini agar stok otomatis berkurang saat buket terjual.</p>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-pink-500 focus:ring-pink-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktif (Tampil di Kasir)</label>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-colors">
                        Simpan Buket
                    </button>
                    @if($editId)
                        <button type="button" wire:click="cancelEdit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-sm transition-colors">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Area -->
    <div class="lg:w-2/3 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden self-start">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap xl:whitespace-normal">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-semibold text-gray-600">Produk</th>
                        <th class="p-4 font-semibold text-gray-600">Harga</th>
                        <th class="p-4 font-semibold text-gray-600">Bahan Baku (Resep)</th>
                        <th class="p-4 font-semibold text-gray-600 text-center">Status</th>
                        <th class="p-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$product->is_active ? 'opacity-50' : '' }}">
                            <td class="p-4 font-medium text-gray-800 align-top">
                                {{ $product->name }}
                            </td>
                            <td class="p-4 font-bold text-pink-600 align-top">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-sm text-gray-600 align-top">
                                @if($product->rawMaterials->count() > 0)
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($product->rawMaterials as $material)
                                            <li>{{ $material->pivot->quantity }} {{ $material->unit }} {{ $material->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic">Belum ada resep</span>
                                @endif
                            </td>
                            <td class="p-4 text-center align-top">
                                @if($product->is_active)
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium border border-green-200">Aktif</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full font-medium border border-gray-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-4 text-right align-top space-x-2">
                                <button wire:click="editProduct({{ $product->id }})" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded transition-colors font-medium text-sm">Edit</button>
                                <button wire:click="deleteProduct({{ $product->id }})" onclick="confirm('Yakin ingin menghapus produk ini secara permanen?') || event.stopImmediatePropagation()" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors font-medium text-sm">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Data katalog produk kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
