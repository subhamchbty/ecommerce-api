<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing products.
     *
     * @return ProductCollection
     */
    public function index(Request $request): ProductCollection | JsonResponse
    {
        try {
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;
            $term = $request->term ?? null;

            $products = Product::with('variants')
                ->when($term, function ($query, $term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhereHas('variants', function ($query) use ($term) {
                            $query->where('name', 'like', "%{$term}%");
                        });
                })
                ->paginate($perPage, ['*'], 'page', $page);

            return new ProductCollection($products);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Store a new product.
     *
     * @param  ProductRequest  $request
     * @return ProductResource
     */
    public function store(ProductRequest $request): ProductResource | JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::create($request->only('name', 'description', 'base_price', 'is_active'));

            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }

            DB::commit();

            return (new ProductResource($product->with('variants')->find($product->id)))->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified product.
     *
     * @param  Product  $product
     * @return ProductResource
     */
    public function show(Product $product): ProductResource | JsonResponse
    {
        try {
            return new ProductResource($product->with('variants')->find($product->id));
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified product.
     *
     * @param  ProductRequest  $request
     * @param  Product  $product
     * @return ProductResource
     */
    public function update(ProductRequest $request, Product $product): ProductResource | JsonResponse
    {
        $productService = new ProductService($request->variants);

        if (count($productService->checkMissingIds($product->id)) > 0) {
            return response()->json(['message' => 'One or more variant IDs are not found in the database'], 404);
        }

        try {
            DB::beginTransaction();

            $product->update($request->only('name', 'description', 'base_price', 'is_active'));

            $productService->syncVariants($product);

            DB::commit();

            return new ProductResource($product->fresh(['variants']));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified product.
     *
     * @param  Product  $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
