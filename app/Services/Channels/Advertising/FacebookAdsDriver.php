<?php

namespace App\Services\Channels\Advertising;

use App\Services\Channels\AbstractDriver;

class FacebookAdsDriver extends AbstractDriver
{
    public function testConnection(): void
    {
        // TODO: Meta Marketing API connection test
        // Requires user access token + ad account ID
        throw new \RuntimeException('Facebook Ads driver not yet implemented.');
    }
}
