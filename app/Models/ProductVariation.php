<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'nome_variacao',
        'quantidade_estoque',
        'preco',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductVariationImage::class, 'variation_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }
}