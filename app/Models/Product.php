<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'double',
        'is_active' => 'boolean',
    ];

    public static function newFactory()
    {
        return \Database\Factories\ProductFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $product->name));
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
