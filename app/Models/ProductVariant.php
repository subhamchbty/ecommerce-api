<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'additional_cost',
        'stock_count',
    ];

    protected $casts = [
        'additional_cost' => 'double',
    ];

    // new factory method
    protected static function newFactory()
    {
        return \Database\Factories\ProductVariantFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            $variant->sku = strtoupper(substr(md5(microtime()), rand(0, 26), 5));
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
