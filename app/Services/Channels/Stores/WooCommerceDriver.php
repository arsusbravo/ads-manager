<?php

namespace App\Services\Channels\Stores;

use App\Services\Channels\AbstractDriver;
use Illuminate\Support\Facades\Http;

class WooCommerceDriver extends AbstractDriver
{
    private function baseUrl(): string
    {
        $url = rtrim($this->credentials()['site_url'], '/');
        return "{$url}/wp-json/wc/v3";
    }

    private function auth(): array
    {
        return [
            $this->credentials()['consumer_key'],
            $this->credentials()['consumer_secret'],
        ];
    }

    public function testConnection(): void
    {
        $response = Http::withBasicAuth(...$this->auth())
            ->get($this->baseUrl() . '/system_status');

        if (! $response->successful()) {
            throw new \RuntimeException('WooCommerce connection failed: ' . $response->status());
        }
    }

    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        $response = Http::withBasicAuth(...$this->auth())
            ->get($this->baseUrl() . '/products', [
                'per_page' => $perPage,
                'page' => $page,
                'status' => 'publish',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('WooCommerce fetchProducts failed: ' . $response->status());
        }

        return array_map([$this, 'normalizeProduct'], $response->json());
    }

    private function normalizeProduct(array $item): array
    {
        return [
            'external_id'  => (string) $item['id'],
            'title'        => $item['name'] ?? '',
            'description'  => strip_tags($item['description'] ?? ''),
            'price'        => (float) ($item['price'] ?? 0),
            'stock'        => (int) ($item['stock_quantity'] ?? 0),
            'sku'          => $item['sku'] ?? null,
            'images'       => array_map(fn($img) => $img['src'], $item['images'] ?? []),
            'categories'   => array_map(fn($cat) => $cat['name'], $item['categories'] ?? []),
            'attributes'   => array_map(fn($attr) => [
                'name' => $attr['name'],
                'values' => $attr['options'] ?? [],
            ], $item['attributes'] ?? []),
            'variants'     => array_map(fn($v) => [
                'external_id' => (string) $v['id'],
                'sku'         => $v['sku'] ?? null,
                'price'       => (float) ($v['price'] ?? 0),
                'stock'       => (int) ($v['stock_quantity'] ?? 0),
                'attributes'  => array_column(
                    array_map(fn($a) => [$a['name'] => $a['option']], $v['attributes'] ?? []),
                    null
                ),
            ], $item['variations_data'] ?? []),
        ];
    }
}
