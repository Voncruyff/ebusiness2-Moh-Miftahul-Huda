<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'description',
        'purchase_price',
        'selling_price',
        'stock',
        'unit',
        'image',
        'status',
    ];

    // 🚫 HAPUS CAST DECIMAL:2 KARENA INI YANG MENAMBAH NOL
    protected $casts = [
        'purchase_price' => 'integer',
        'selling_price' => 'integer',
    ];


    // Format untuk tampilan
    public function getFormattedSellingPriceAttribute()
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getFormattedPurchasePriceAttribute()
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    // Filter produk aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
