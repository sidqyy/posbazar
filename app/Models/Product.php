<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class)->withPivot('quantity')->withTimestamps();
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
