<?php

namespace App\Services\Channels\Stores;

use App\Services\Channels\AbstractDriver;
use Illuminate\Support\Facades\Http;

class MagentoDriver extends AbstractDriver
{
    private function baseUrl(): string
    {
        return rtrim($this->credentials()['base_url'], '/') . '/rest/V1';
    }

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->credentials()['access_token']];
    }

    public function testConnection(): void
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . '/store/storeConfigs');

        if (! $response->successful()) {
            throw new \RuntimeException('Magento connection failed: ' . $response->status());
        }
    }

    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        $currentPage = $page;

        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . '/products', [
                'searchCriteria[pageSize]'    => $perPage,
                'searchCriteria[currentPage]' => $currentPage,
                'searchCriteria[filter_groups][0][filters][0][field]'      => 'type_id',
                'searchCriteria[filter_groups][0][filters][0][value]'      => 'simple,configurable',
                'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'in',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Magento fetchProducts failed: ' . $response->status());
        }

        $items = $response->json('items', []);

        return array_map([$this, 'normalizeProduct'], $items);
    }

    private function normalizeProduct(array $item): array
    {
        $customAttributes = [];
        foreach ($item['custom_attributes'] ?? [] as $attr) {
            $customAttributes[$attr['attribute_code']] = $attr['value'];
        }

        $mediaGallery = array_filter(
            $item['media_gallery_entries'] ?? [],
            fn($e) => in_array('image', $e['types'] ?? []) || $e['media_type'] === 'image'
        );

        return [
            'external_id'  => (string) $item['id'],
            'title'        => $item['name'] ?? '',
            'description'  => strip_tags($customAttributes['description'] ?? ''),
            'price'        => (float) ($item['price'] ?? 0),
            'stock'        => (int) ($item['extension_attributes']['stock_item']['qty'] ?? 0),
            'sku'          => $item['sku'] ?? null,
            'images'       => array_map(
                fn($e) => rtrim($this->credentials()['base_url'], '/') . '/pub/media/catalog/product' . $e['file'],
                array_values($mediaGallery)
            ),
            'categories'   => array_map(
                fn($id) => (string) $id,
                $item['extension_attributes']['category_links'] ?? []
            ),
            'attributes'   => [], // Extended attributes fetched separately if needed
            'variants'     => [], // Magento configurable children fetched separately
        ];
    }
}
