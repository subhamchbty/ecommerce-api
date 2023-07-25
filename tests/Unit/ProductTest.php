<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public $productStructure = [
        'id',
        'name',
        'description',
        'base_price',
        'is_active',
        'variants' => [
            '*' => [
                'id',
                'name',
                'additional_cost',
                'stock_count',
            ]
        ],
        'created_at',
        'updated_at',
    ];

    public $productInsertData = [
        'name' => 'Product iPhone 123',
        'description' => 'Product 1 description',
        'base_price' => 1000,
        'is_active' => true,
        'variants' => [
            [
                'name' => 'Variant 1',
                'additional_cost' => 100,
                'stock_count' => 10,
            ],
            [
                'name' => 'Variant 2',
                'additional_cost' => 200,
                'stock_count' => 20,
            ],
        ]
    ];

    public function test_product_index_endpoint()
    {
        Product::factory()->has(ProductVariant::factory()->count(2), 'variants')->create();

        $response = $this->getJson('/api/products');
        $products = $response->json('products');

        $response
            ->assertStatus(200);

        $this->assertTrue(count($products) > 0);
    }

    public function test_product_search_endpoint()
    {
        Product::factory()->has(ProductVariant::factory()->count(2), 'variants')->create(['name' => 'iPhone']);
        Product::factory()->has(ProductVariant::factory()->count(3), 'variants')->create(['name' => 'Samsung']);
        Product::factory()->has(ProductVariant::factory()->count(1), 'variants')->create(['name' => 'Pixel']);

        $searchTerm = 'iphone';
        $response = $this->getJson('/api/products?term=' . $searchTerm);
        $products = $response->json('products');

        $response
            ->assertStatus(200);

        $this->assertTrue(count($products) > 0);
    }

    public function test_product_store_endpoint()
    {
        $response = $this->postJson('/api/products', $this->productInsertData);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->productStructure
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->productInsertData['name'],
                    'description' => $this->productInsertData['description'],
                    'base_price' => $this->productInsertData['base_price'],
                    'is_active' => $this->productInsertData['is_active'],
                    'variants' => [
                        [
                            'name' => $this->productInsertData['variants'][0]['name'],
                            'additional_cost' => $this->productInsertData['variants'][0]['additional_cost'],
                            'stock_count' => $this->productInsertData['variants'][0]['stock_count'],
                        ],
                        [
                            'name' => $this->productInsertData['variants'][1]['name'],
                            'additional_cost' => $this->productInsertData['variants'][1]['additional_cost'],
                            'stock_count' => $this->productInsertData['variants'][1]['stock_count'],
                        ],
                    ]
                ]
            ]);
    }

    public function test_product_show_endpoint()
    {
        Product::factory()->has(ProductVariant::factory()->count(2), 'variants')->create();

        $response = $this->getJson('/api/products/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->productStructure
            ]);

        $this->assertTrue($response->json('data.id') == 1);
    }

    public function test_product_update_endpoint()
    {
        Product::factory()->has(ProductVariant::factory()->count(2), 'variants')->create();

        $response = $this->putJson('/api/products/1', $this->productInsertData);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->productStructure
            ])
            ->assertJson([
                'data' => [
                    'name' => $this->productInsertData['name'],
                    'description' => $this->productInsertData['description'],
                    'base_price' => $this->productInsertData['base_price'],
                    'is_active' => $this->productInsertData['is_active'],
                    'variants' => [
                        [
                            'name' => $this->productInsertData['variants'][0]['name'],
                            'additional_cost' => $this->productInsertData['variants'][0]['additional_cost'],
                            'stock_count' => $this->productInsertData['variants'][0]['stock_count'],
                        ],
                        [
                            'name' => $this->productInsertData['variants'][1]['name'],
                            'additional_cost' => $this->productInsertData['variants'][1]['additional_cost'],
                            'stock_count' => $this->productInsertData['variants'][1]['stock_count'],
                        ],
                    ]
                ]
            ]);
    }

    public function test_product_delete_endpoint()
    {
        Product::factory()->has(ProductVariant::factory()->count(2), 'variants')->create();

        $response = $this->deleteJson('/api/products/1');

        $response
            ->assertStatus(200)
            ->assertExactJson(
                ['message' => 'Product deleted successfully']
            );
    }
}
