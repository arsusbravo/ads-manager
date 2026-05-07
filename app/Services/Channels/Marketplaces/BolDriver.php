<?php

namespace App\Services\Channels\Marketplaces;

use App\Services\Channels\AbstractDriver;
use Illuminate\Support\Facades\Http;

class BolDriver extends AbstractDriver
{
    private ?string $bearerToken = null;

    private function baseUrl(): string
    {
        return 'https://api.bol.com/retailer';
    }

    private function getToken(): string
    {
        if ($this->bearerToken) return $this->bearerToken;

        $creds = $this->credentials();
        $response = Http::asForm()->post('https://login.bol.com/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('BOL.com auth failed: ' . $response->status());
        }

        $this->bearerToken = $response->json('access_token');
        return $this->bearerToken;
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Accept'        => 'application/vnd.retailer.v10+json',
            'Content-Type'  => 'application/vnd.retailer.v10+json',
        ];
    }

    public function testConnection(): void
    {
        Http::withHeaders($this->headers())->get($this->baseUrl() . '/offers?page=1&limit=1');
    }

    public function pushProduct(array $productData): string
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl() . '/offers', [
                'ean'      => $productData['ean'] ?? $productData['sku'],
                'condition'=> ['name' => 'NEW'],
                'pricing'  => ['bundlePrices' => [['quantity' => 1, 'unitPrice' => $productData['price']]]],
                'stock'    => ['amount' => $productData['stock'], 'managedByRetailer' => true],
                'fulfilment' => ['method' => 'FBR'],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('BOL.com pushProduct failed: ' . $response->body());
        }

        return $response->json('offerId', '');
    }

    public function removeProduct(string $externalId): void
    {
        Http::withHeaders($this->headers())->delete($this->baseUrl() . "/offers/{$externalId}");
    }
}
