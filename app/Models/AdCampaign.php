<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'user_id', 'channel_integration_id', 'name', 'status',
        'budget', 'ai_content', 'external_campaign_id', 'targeting',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'ai_content' => 'array',
            'targeting' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channelIntegration()
    {
        return $this->belongsTo(ChannelIntegration::class);
    }
}
