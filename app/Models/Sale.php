<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['total_amount', 'customer_name', 'payment_method', 'amount_tendered', 'change_amount'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
