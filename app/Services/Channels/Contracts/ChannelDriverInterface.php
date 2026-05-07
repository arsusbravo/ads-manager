<?php

namespace App\Services\Channels\Contracts;

use App\Models\ChannelIntegration;

interface ChannelDriverInterface
{
    public function __construct(ChannelIntegration $integration);

    /** Test the connection. Throws \Exception on failure. */
    public function testConnection(): void;

    /** Return an OAuth URL to redirect the user to, or null if not OAuth-based. */
    public function getAuthUrl(): ?string;

    /** Handle the OAuth callback payload and persist tokens to the integration. */
    public function handleOAuthCallback(array $params): void;

    /**
     * Fetch all products from the channel and return them as a normalized array.
     * Each item must have: external_id, title, description, price, stock, sku, images[], categories[], attributes[], variants[]
     */
    public function fetchProducts(int $page = 1, int $perPage = 100): array;

    /**
     * Push a product (or update it) on the channel.
     * Returns the external listing ID.
     */
    public function pushProduct(array $productData): string;

    /** Remove a product from the channel. */
    public function removeProduct(string $externalId): void;
}
