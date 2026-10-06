<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name', 120);
            $table->string('tagline', 180)->nullable();
            $table->string('public_email')->nullable();
            $table->string('primary_phone', 50)->nullable();
            $table->string('secondary_phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('facebook_url', 2048)->nullable();
            $table->string('instagram_url', 2048)->nullable();
            $table->string('linkedin_url', 2048)->nullable();
            $table->string('youtube_url', 2048)->nullable();
            $table->string('x_twitter_url', 2048)->nullable();
            $table->string('footer_text', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
