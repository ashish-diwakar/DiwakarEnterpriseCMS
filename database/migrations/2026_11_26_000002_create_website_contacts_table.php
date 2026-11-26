<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table): void {
            $table->timestamp('contacts_initialized_at')->nullable();
            $table->unsignedInteger('contacts_version')->default(0);
        });
        Schema::create('website_contacts', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('website_setting_id')->constrained()->restrictOnDelete();
            $table->string('type', 20);
            $table->string('subtype', 20)->nullable();
            $table->string('label', 120)->nullable();
            $table->string('value', 255);
            $table->string('normalized_value', 255);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order');
            $table->string('primary_type', 20)->nullable()->virtualAs('CASE WHEN is_primary = 1 THEN type ELSE NULL END');
            $table->timestamps();
            $table->unique(['website_setting_id', 'type', 'normalized_value'], 'website_contacts_unique_value');
            $table->unique(['website_setting_id', 'primary_type'], 'website_contacts_unique_primary');
            $table->index(['website_setting_id', 'type', 'sort_order', 'id'], 'website_contacts_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_contacts');
        Schema::table('website_settings', function (Blueprint $table): void {
            $table->dropColumn(['contacts_initialized_at', 'contacts_version']);
        });
    }
};
