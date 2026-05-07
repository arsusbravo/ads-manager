<?php

namespace App\Services\Channels\Stores;

use App\Services\Channels\AbstractDriver;
use Illuminate\Support\Facades\Http;

class ShopifyDriver extends AbstractDriver
{
    private function baseUrl(): string
    {
        $domain = rtrim($this->credentials()['shop_domain'], '/');
        return "https://{$domain}/admin/api/2024-01";
    }

    private function headers(): array
    {
        return ['X-Shopify-Access-Token' => $this->credentials()['access_token']];
    }

    public function getAuthUrl(): ?string
    {
        // Shopify OAuth is handled externally via app install flow
        // Return null here; the install URL is constructed separately
        return null;
    }

    public function handleOAuthCallback(array $params): void
    {
        // Exchange the code for a permanent access token
        $domain = $this->credentials()['shop_domain'];
        $response = Http::post("https://{$domain}/admin/oauth/access_token", [
            'client_id'     => config('services.shopify.client_id'),
            'client_secret' => config('services.shopify.client_secret'),
            'code'          => $params['code'],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Shopify OAuth token exchange failed.');
        }

        $creds = $this->integration->credentials;
        $creds['access_token'] = $response->json('access_token');
        $this->integration->credentials = $creds;
        $this->integration->save();
    }

    public function testConnection(): void
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . '/shop.json');

        if (! $response->successful()) {
            throw new \RuntimeException('Shopify connection failed: ' . $response->status());
        }
    }

    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . '/products.json', [
                'limit' => min($perPage, 250),
                'page_info' => null, // cursor-based pagination handled separately for large stores
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Shopify fetchProducts failed: ' . $response->status());
        }

        return array_map([$this, 'normalizeProduct'], $response->json('products', []));
    }

    private function normalizeProduct(array $item): array
    {
        $variants = $item['variants'] ?? [];
        $firstVariant = $variants[0] ?? [];

        return [
            'external_id'  => (string) $item['id'],
            'title'        => $item['title'] ?? '',
            'description'  => strip_tags($item['body_html'] ?? ''),
            'price'        => (float) ($firstVariant['price'] ?? 0),
            'stock'        => array_sum(array_column($variants, 'inventory_quantity')),
            'sku'          => $firstVariant['sku'] ?? null,
            'images'       => array_map(fn($img) => $img['src'], $item['images'] ?? []),
            'categories'   => [], // Shopify uses collections, not product categories
            'attributes'   => array_map(fn($opt) => [
                'name' => $opt['name'],
                'values' => $opt['values'],
            ], $item['options'] ?? []),
            'variants'     => array_map(fn($v) => [
                'external_id' => (string) $v['id'],
                'sku'         => $v['sku'] ?? null,
                'price'       => (float) ($v['price'] ?? 0),
                'stock'       => (int) ($v['inventory_quantity'] ?? 0),
                'attributes'  => array_filter([
                    $v['option1'] ? ['option1' => $v['option1']] : null,
                    $v['option2'] ? ['option2' => $v['option2']] : null,
                    $v['option3'] ? ['option3' => $v['option3']] : null,
                ]),
            ], $variants),
        ];
    }
}
