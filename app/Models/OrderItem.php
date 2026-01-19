<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'design_id',
        'quantity',
        'unit_price',
        'total_price',
        'design_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'design_snapshot' => 'array',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Calculate total price based on quantity and unit price
     */
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
