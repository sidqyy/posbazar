<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Manajemen Stok Bahan Baku</h2>

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Form Area -->
    <div class="lg:w-1/3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">
                {{ $editId ? 'Edit Bahan Baku' : 'Tambah Bahan Baku' }}
            </h3>
            <form wire:submit.prevent="saveMaterial" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan</label>
                    <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan (Unit)</label>
                    <input type="text" wire:model="unit" placeholder="Misal: Tangkai, Lembar, Roll" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                    <input type="number" wire:model="stock" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-2" required min="0">
                </div>
                <div class="pt-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-colors">
                        Simpan
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
            <table class="w-full text-left border-collapse whitespace-nowrap md:whitespace-normal">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-semibold text-gray-600">Nama Bahan</th>
                        <th class="p-4 font-semibold text-gray-600 text-center">Stok</th>
                        <th class="p-4 font-semibold text-gray-600 text-center">Unit</th>
                        <th class="p-4 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($materials as $material)
                        <tr class="hover:bg-gray-50 transition-colors {{ $material->stock < 10 ? 'bg-red-50' : '' }}">
                            <td class="p-4 font-medium text-gray-800 flex items-center gap-3">
                                @if($material->stock < 10)
                                    <span class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                @else
                                    <span class="h-3 w-3 rounded-full bg-green-400"></span>
                                @endif
                                {{ $material->name }}
                            </td>
                            <td class="p-4 text-center font-bold text-lg {{ $material->stock < 10 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $material->stock }}
                            </td>
                            <td class="p-4 text-center text-gray-500">
                                {{ $material->unit }}
                            </td>
                            <td class="p-4 text-right space-y-2 sm:space-y-0">
                                <!-- Quick actions -->
                                <div class="inline-flex items-center bg-gray-100 rounded-lg p-1 mr-2">
                                    <button wire:click="decrementStock({{ $material->id }})" class="w-7 h-7 rounded bg-white text-gray-600 shadow-sm hover:bg-gray-50 active:bg-gray-200 flex items-center justify-center font-bold">-</button>
                                    <button wire:click="incrementStock({{ $material->id }})" class="w-7 h-7 rounded bg-white text-gray-600 shadow-sm hover:bg-gray-50 active:bg-gray-200 flex items-center justify-center font-bold ml-1">+</button>
                                </div>
                                
                                <!-- Edit / Delete -->
                                <button wire:click="editMaterial({{ $material->id }})" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded">Edit</button>
                                <button wire:click="deleteMaterial({{ $material->id }})" onclick="confirm('Yakin ingin menghapus bahan baku ini?') || event.stopImmediatePropagation()" class="text-red-500 hover:bg-red-50 p-1.5 rounded">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Data bahan baku kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
