<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_integration_id')->constrained()->cascadeOnDelete();
            $table->string('external_listing_id')->nullable();
            $table->enum('status', ['pending', 'active', 'error', 'delisted'])->default('pending');
            $table->text('error_message')->nullable();
            $table->longText('listing_data')->nullable(); // channel-specific overrides
            $table->timestamp('last_pushed_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'channel_integration_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_listings');
    }
};
