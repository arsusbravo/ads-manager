<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelListing extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'channel_integration_id',
        'external_listing_id', 'status', 'error_message',
        'listing_data', 'last_pushed_at',
    ];

    protected function casts(): array
    {
        return [
            'listing_data' => 'array',
            'last_pushed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function channelIntegration()
    {
        return $this->belongsTo(ChannelIntegration::class);
    }
}
