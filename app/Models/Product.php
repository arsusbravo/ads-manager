<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'store_id', 'external_id', 'title', 'description',
        'price', 'stock', 'sku', 'images', 'categories', 'attributes',
        'raw_data', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'images' => 'array',
            'categories' => 'array',
            'attributes' => 'array',
            'raw_data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function listings()
    {
        return $this->hasMany(ChannelListing::class);
    }

    public function aiContentRequests()
    {
        return $this->hasMany(AiContentRequest::class);
    }
}
