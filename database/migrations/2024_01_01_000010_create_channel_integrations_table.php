<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel_type'); // woocommerce, shopify, magento, bol, amazon, google_ads, facebook_ads
            $table->string('name');
            $table->text('credentials'); // encrypted JSON blob
            $table->longText('meta')->nullable(); // channel-specific settings
            $table->enum('status', ['active', 'inactive', 'error'])->default('inactive');
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_integrations');
    }
};
