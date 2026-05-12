<?php

namespace App\Services\Channels\Stores;

use App\Services\Channels\AbstractDriver;
use Illuminate\Support\Facades\Http;

class CsCartDriver extends AbstractDriver
{
    private function baseUrl(): string
    {
        return rtrim($this->credentials()['store_url'], '/') . '/api';
    }

    private function auth(): array
    {
        return [
            $this->credentials()['api_email'],
            $this->credentials()['api_key'],
        ];
    }

    public function testConnection(): void
    {
        $response = Http::withBasicAuth(...$this->auth())
            ->get($this->baseUrl() . '/products', ['items_per_page' => 1]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'CS-Cart connection failed: ' . ($response->json('message') ?? $response->status())
            );
        }
    }

    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        $response = Http::withBasicAuth(...$this->auth())
            ->get($this->baseUrl() . '/products', [
                'page'           => $page,
                'items_per_page' => $perPage,
                'status'         => 'A',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('CS-Cart fetchProducts failed: ' . $response->status());
        }

        $body = $response->json();
        $products = $body['products'] ?? [];

        if (! is_array($products)) {
            throw new \RuntimeException('CS-Cart returned an unexpected response.');
        }

        return array_map([$this, 'normalizeProduct'], $products);
    }

    private function normalizeProduct(array $item): array
    {
        $image = $item['main_pair']['detailed']['image_path']
            ?? $item['main_pair']['icon']['image_path']
            ?? null;

        return [
            'external_id'  => (string) $item['product_id'],
            'title'        => $item['product'] ?? '',
            'description'  => strip_tags($item['full_description'] ?? $item['short_description'] ?? ''),
            'price'        => (float) ($item['price'] ?? 0),
            'stock'        => (int) ($item['amount'] ?? 0),
            'sku'          => $item['product_code'] ?? null,
            'images'       => $image ? [$image] : [],
            'categories'   => array_values(array_filter((array) ($item['category_ids'] ?? []))),
            'attributes'   => [],
            'variants'     => [],
        ];
    }
}
