<?php

namespace App\Services\Channels\Marketplaces;

use App\Services\Channels\AbstractDriver;

class AmazonDriver extends AbstractDriver
{
    public function testConnection(): void
    {
        // TODO: Amazon SP-API connection test
        // Requires LWA (Login with Amazon) OAuth + SP-API signature
        throw new \RuntimeException('Amazon driver not yet implemented.');
    }

    public function pushProduct(array $productData): string
    {
        // TODO: Amazon SP-API Listings Items API
        throw new \RuntimeException('Amazon driver not yet implemented.');
    }
}
