<?php

namespace App\Services\Channels;

use App\Models\ChannelIntegration;
use App\Services\Channels\Contracts\ChannelDriverInterface;
use App\Services\Channels\Stores\WooCommerceDriver;
use App\Services\Channels\Stores\ShopifyDriver;
use App\Services\Channels\Stores\MagentoDriver;
use App\Services\Channels\Marketplaces\BolDriver;
use App\Services\Channels\Marketplaces\AmazonDriver;
use App\Services\Channels\Advertising\GoogleAdsDriver;
use App\Services\Channels\Advertising\FacebookAdsDriver;
use InvalidArgumentException;

class ChannelManager
{
    /** Human-readable labels keyed by channel_type slug */
    public const TYPES = [
        'woocommerce'  => 'WooCommerce',
        'shopify'      => 'Shopify',
        'magento'      => 'Magento 2',
        'bol'          => 'BOL.com',
        'amazon'       => 'Amazon',
        'google_ads'   => 'Google Ads',
        'facebook_ads' => 'Facebook Ads',
    ];

    private static array $drivers = [
        'woocommerce'  => WooCommerceDriver::class,
        'shopify'      => ShopifyDriver::class,
        'magento'      => MagentoDriver::class,
        'bol'          => BolDriver::class,
        'amazon'       => AmazonDriver::class,
        'google_ads'   => GoogleAdsDriver::class,
        'facebook_ads' => FacebookAdsDriver::class,
    ];

    public function driver(ChannelIntegration $integration): ChannelDriverInterface
    {
        $class = self::$drivers[$integration->channel_type] ?? null;

        if (! $class) {
            throw new InvalidArgumentException("No driver for channel type: {$integration->channel_type}");
        }

        return new $class($integration);
    }
}
