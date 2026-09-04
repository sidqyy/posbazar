<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RawMaterial;

class InventoryManager extends Component
{
    public string $name = '';
    public string $unit = '';
    public int $stock = 0;
    public ?int $editId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'unit' => 'required|string|max:50',
        'stock' => 'required|integer|min:0',
    ];

    public function saveMaterial()
    {
        $this->validate();

        if ($this->editId) {
            $material = RawMaterial::find($this->editId);
            $oldStock = $material->stock;
            $material->update([
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => $this->stock,
            ]);

            if ($oldStock != $this->stock) {
                $diff = $this->stock - $oldStock;
                \App\Models\StockMutation::create([
                    'raw_material_id' => $material->id,
                    'type' => $diff > 0 ? 'in' : 'out',
                    'quantity' => abs($diff),
                    'description' => 'Penyesuaian manual',
                ]);
            }

            session()->flash('success', 'Bahan baku berhasil diubah!');
        } else {
            $material = RawMaterial::create([
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => $this->stock,
            ]);
            
            if ($this->stock > 0) {
                \App\Models\StockMutation::create([
                    'raw_material_id' => $material->id,
                    'type' => 'in',
                    'quantity' => $this->stock,
                    'description' => 'Stok awal',
                ]);
            }

            session()->flash('success', 'Bahan baku berhasil ditambahkan!');
        }

        $this->reset(['name', 'unit', 'stock', 'editId']);
    }

    public function editMaterial(int $id)
    {
        $material = RawMaterial::findOrFail($id);
        $this->editId = $material->id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->stock = $material->stock;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'unit', 'stock', 'editId']);
    }

    public function deleteMaterial(int $id)
    {
        $material = RawMaterial::findOrFail($id);
        if ($material->products()->exists()) {
            session()->flash('error', 'Gagal: Bahan baku sedang digunakan dalam resep buket!');
            return;
        }
        $material->delete();
        session()->flash('success', 'Bahan baku berhasil dihapus!');
    }

    public function incrementStock(int $id)
    {
        RawMaterial::where('id', $id)->increment('stock', 1);
        \App\Models\StockMutation::create([
            'raw_material_id' => $id,
            'type' => 'in',
            'quantity' => 1,
            'description' => 'Penyesuaian manual',
        ]);
    }

    public function decrementStock(int $id)
    {
        $material = RawMaterial::find($id);
        if ($material && $material->stock > 0) {
            $material->decrement('stock', 1);
            \App\Models\StockMutation::create([
                'raw_material_id' => $id,
                'type' => 'out',
                'quantity' => 1,
                'description' => 'Penyesuaian manual',
            ]);
        }
    }

    public function render()
    {
        $materials = RawMaterial::all();
        return view('livewire.inventory-manager', compact('materials'));
    }
}
