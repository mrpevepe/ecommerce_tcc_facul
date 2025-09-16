<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the product variations associated with this size.
     */
    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_sizes')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}