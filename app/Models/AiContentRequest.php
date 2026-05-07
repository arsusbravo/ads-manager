<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiContentRequest extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'context_type', 'prompt',
        'result', 'model', 'status', 'error',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
