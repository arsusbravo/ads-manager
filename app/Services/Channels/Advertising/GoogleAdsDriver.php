<?php

namespace App\Services\Channels\Advertising;

use App\Services\Channels\AbstractDriver;

class GoogleAdsDriver extends AbstractDriver
{
    public function testConnection(): void
    {
        // TODO: Google Ads API connection test
        // Requires OAuth 2.0 + developer token
        throw new \RuntimeException('Google Ads driver not yet implemented.');
    }
}
