<?php

namespace App\Services\Product;

use App\Enums\EProductStatuses;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Service for products management
 */
class ProductsManageService
{

    /**
     * @param ProductsCategoriesManageService $pCategoriesService
     * @param ProductsTagsManageService $pTagsService
     * @return void
     */
    public function __construct(ProductsCategoriesManageService $pCategoriesService, ProductsTagsManageService $pTagsService)
    {
        $this->productsCategoriesManageService = $pCategoriesService;
        $this->productsTagsManageService = $pTagsService;
    }


    /**
     * Creates new product.
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        $result = null;
        DB::beginTransaction();
        try {
            // 1. Create product
            /** @var Product $newProduct */
            $newProduct = Product::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'],
                // Status for creation, below will be used setNewStatus method
                'status' => EProductStatuses::DRAFT,
                'brand_id' => $data['brand_id'],
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);

            // 2. Set price
            if ($data['price'] > 0) {
                $newProduct->setPrice($data['price'], $data['old_price']);
            }

            // 3. Set status
            $newProduct->setNewStatus($data['status']);

            // 4. Assign categories
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $oneCatId) {
                    $this->productsCategoriesManageService->assignCategory($newProduct, $oneCatId);
                }
            }

            // 5a. Assign existing tags
            if (isset($data['exist_tags'])) {
                foreach ($data['exist_tags'] as $existTagId) {
                    $this->productsTagsManageService->assignExistingTag($newProduct, $existTagId);
                }
            }
            // 5b. Create and assign new tags
            if (isset($data['new_tags'])) {
                $newTags = explode("\n", $data['new_tags']);
                foreach ($newTags as $newTagName) {
                    $this->productsTagsManageService->assignNewTag($newProduct, $newTagName);
                }
            }

            // 6. Set characteristics
            // TODO

            DB::commit();
            $result = $newProduct;
        } catch(Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $result;
    }


    /**
     * Updates the product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function update(Product $product, array $data): Product
    {
        DB::beginTransaction();
        try {
            // 1. Update product
            $product->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'],
                'brand_id' => $data['brand_id'],
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);

            // 2. Set price
            if ($data['price'] > 0) {
                $product->setPrice($data['price'], $data['old_price']);
            }

            // 3. Set status
            $product->setNewStatus($data['status']);

            // 4a. Revoke all categories
            $this->productsCategoriesManageService->revokeAllCategories($product);
            // 4b. Assign categories
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $oneCatId) {
                    $this->productsCategoriesManageService->assignCategory($product, $oneCatId);
                }
            }

            // 5a. Revoke all tags
            $this->productsTagsManageService->revokeAllTags($product);
            // 5b. Assign existing tags
            if (isset($data['exist_tags'])) {
                foreach ($data['exist_tags'] as $existTagId) {
                    $this->productsTagsManageService->assignExistingTag($product, $existTagId);
                }
            }
            // 5c. Create and assign new tags
            if (isset($data['new_tags'])) {
                $newTags = explode("\n", $data['new_tags']);
                foreach ($newTags as $newTagName) {
                    $this->productsTagsManageService->assignNewTag($product, $newTagName);
                }
            }

            // 6. Set characteristics
            // TODO

            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $product;
    }

}
