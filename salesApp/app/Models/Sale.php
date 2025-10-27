<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'customer_name',
        'inventory_id',
        'product_name',
        'unit_price',
        'quantity',
        'total_amount',
        'sale_date',
        'sales_person',
        'status'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationship with Inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Calculate total amount automatically
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($sale) {
            $sale->total_amount = $sale->unit_price * $sale->quantity;
        });
    }
}