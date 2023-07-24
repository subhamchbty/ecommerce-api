<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductService
{
    public $variantIds;

    public function __construct(public $variants)
    {
        $this->variantIds = collect($variants)->pluck('id')->filter();
    }

    public function checkMissingIds(int $productId)
    {
        $existingVariantIds = ProductVariant::whereIn('id', $this->variantIds)->where('product_id', $productId)->pluck('id')->toArray();

        return array_diff($this->variantIds->toArray(), $existingVariantIds);
    }

    public function syncVariants(Product $product)
    {
        $product->variants()->whereNotIn('id', $this->variantIds)->delete();

        foreach ($this->variants as $variant) {
            $variantId = $variant['id'] ?? null;

            $product->variants()->updateOrCreate(['id' => $variantId], $variant);
        }
    }
}
