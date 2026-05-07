<?php

namespace App\Services;

use App\Models\Store;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Channels\ChannelManager;

class ProductImportService
{
    public function __construct(private ChannelManager $channelManager) {}

    public function importFromStore(Store $store): array
    {
        $driver = $this->channelManager->driver($store->channelIntegration);

        $imported = 0;
        $page = 1;

        do {
            $products = $driver->fetchProducts($page, 100);

            foreach ($products as $data) {
                $this->upsertProduct($store, $data);
                $imported++;
            }

            $page++;
        } while (count($products) === 100);

        return ['imported' => $imported];
    }

    private function upsertProduct(Store $store, array $data): Product
    {
        $product = Product::updateOrCreate(
            ['store_id' => $store->id, 'external_id' => $data['external_id']],
            [
                'user_id'     => $store->user_id,
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'] ?? null,
                'stock'       => $data['stock'] ?? 0,
                'sku'         => $data['sku'] ?? null,
                'images'      => $data['images'] ?? [],
                'categories'  => $data['categories'] ?? [],
                'attributes'  => $data['attributes'] ?? [],
                'raw_data'    => $data,
            ]
        );

        foreach ($data['variants'] ?? [] as $variantData) {
            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'external_id' => $variantData['external_id']],
                [
                    'sku'        => $variantData['sku'] ?? null,
                    'price'      => $variantData['price'] ?? null,
                    'stock'      => $variantData['stock'] ?? 0,
                    'attributes' => $variantData['attributes'] ?? [],
                ]
            );
        }

        return $product;
    }
}
