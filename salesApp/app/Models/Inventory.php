<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Specify the table name explicitly
    protected $table = 'inventory';

    protected $fillable = [
        'product_code',
        'product_name',
        'description',
        'unit_price',
        'stock_quantity',
        'category',
        'supplier',
        'last_restocked'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'last_restocked' => 'date',
    ];

    // Relationship with Sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Helper method to check if product is in stock
    public function isInStock($quantity = 1)
    {
        return $this->stock_quantity >= $quantity;
    }

    // Helper method to reduce stock
    public function reduceStock($quantity)
    {
        if ($this->isInStock($quantity)) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }
}