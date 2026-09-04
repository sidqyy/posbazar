<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    protected $fillable = ['raw_material_id', 'type', 'quantity', 'description'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
