<?php

namespace App\Services\Channels;

use App\Models\ChannelIntegration;
use App\Services\Channels\Contracts\ChannelDriverInterface;

abstract class AbstractDriver implements ChannelDriverInterface
{
    public function __construct(protected ChannelIntegration $integration) {}

    public function getAuthUrl(): ?string
    {
        return null;
    }

    public function handleOAuthCallback(array $params): void
    {
        // Override in OAuth-based drivers
    }

    public function pushProduct(array $productData): string
    {
        throw new \BadMethodCallException(static::class . ' does not support pushProduct.');
    }

    public function removeProduct(string $externalId): void
    {
        throw new \BadMethodCallException(static::class . ' does not support removeProduct.');
    }

    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        throw new \BadMethodCallException(static::class . ' does not support fetchProducts.');
    }

    protected function credentials(): array
    {
        return $this->integration->credentials;
    }
}
