<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\RawMaterial;

class ProductManager extends Component
{
    public $name = '';
    public $price = '';
    public $is_active = true;

    // Materials state for the new product
    public $selectedMaterials = []; // format: [['material_id' => x, 'quantity' => y]]

    public function mount()
    {
        $this->addMaterialRow();
    }

    public function addMaterialRow()
    {
        $this->selectedMaterials[] = ['material_id' => '', 'quantity' => 1];
    }

    public function removeMaterialRow(int $index)
    {
        unset($this->selectedMaterials[$index]);
        $this->selectedMaterials = array_values($this->selectedMaterials);
    }

    public ?int $editId = null;

    public function saveProduct()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($this->editId) {
            $product = Product::find($this->editId);
            $product->update([
                'name' => $this->name,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Katalog produk berhasil diubah!');
        } else {
            $product = Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Katalog produk berhasil ditambahkan!');
        }

        // Attach materials
        $attachData = [];
        foreach ($this->selectedMaterials as $mat) {
            if (!empty($mat['material_id']) && $mat['quantity'] > 0) {
                $attachData[$mat['material_id']] = ['quantity' => $mat['quantity']];
            }
        }

        $product->rawMaterials()->sync($attachData);
        
        $this->reset(['name', 'price', 'is_active', 'selectedMaterials', 'editId']);
        $this->addMaterialRow();
    }

    public function editProduct(int $id)
    {
        $product = Product::with('rawMaterials')->findOrFail($id);
        $this->editId = $product->id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->is_active = $product->is_active;
        
        $this->selectedMaterials = [];
        foreach ($product->rawMaterials as $mat) {
            $this->selectedMaterials[] = [
                'material_id' => $mat->id,
                'quantity' => $mat->pivot->quantity
            ];
        }
        
        if (empty($this->selectedMaterials)) {
            $this->addMaterialRow();
        }
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'price', 'is_active', 'selectedMaterials', 'editId']);
        $this->addMaterialRow();
    }

    public function deleteProduct(int $id)
    {
        $product = Product::findOrFail($id);
        $product->rawMaterials()->detach();
        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus!');
    }

    public function render()
    {
        $products = Product::with('rawMaterials')->get();
        $rawMaterials = RawMaterial::all();
        
        return view('livewire.product-manager', compact('products', 'rawMaterials'));
    }
}
