<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table): void {
            $table->timestamp('addresses_initialized_at')->nullable();
        });
        Schema::create('website_addresses', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('website_setting_id')->constrained()->restrictOnDelete();
            $table->string('label', 120);
            $table->text('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->string('country', 120)->nullable();
            $table->char('normalized_hash', 64);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order');
            $table->unsignedTinyInteger('primary_key')->nullable()->virtualAs('CASE WHEN is_primary = 1 THEN 1 ELSE NULL END');
            $table->timestamps();
            $table->unique(['website_setting_id', 'id'], 'website_addresses_owner_id');
            $table->unique(['website_setting_id', 'primary_key'], 'website_addresses_primary');
            $table->unique(['website_setting_id', 'normalized_hash'], 'website_addresses_duplicate');
            $table->index(['website_setting_id', 'sort_order', 'id'], 'website_addresses_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_addresses');
        Schema::table('website_settings', fn (Blueprint $table) => $table->dropColumn('addresses_initialized_at'));
    }
};
