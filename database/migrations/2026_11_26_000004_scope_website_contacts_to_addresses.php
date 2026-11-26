<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_contacts', function (Blueprint $table): void {
            $table->dropUnique('website_contacts_unique_value');
            $table->dropUnique('website_contacts_unique_primary');
            $table->unsignedBigInteger('website_address_id')->nullable();
            // Zero is reserved for default website contacts; generated address IDs are positive.
            $table->unsignedBigInteger('scope_key')->virtualAs('COALESCE(website_address_id, 0)');
            $table->foreign(['website_setting_id', 'website_address_id'], 'website_contacts_address_owner')
                ->references(['website_setting_id', 'id'])->on('website_addresses')->restrictOnDelete();
            $table->unique(['website_setting_id', 'scope_key', 'type', 'normalized_value'], 'website_contacts_unique_value');
            $table->unique(['website_setting_id', 'scope_key', 'primary_type'], 'website_contacts_unique_primary');
            $table->index(['website_setting_id', 'website_address_id', 'type', 'sort_order', 'id'], 'website_contacts_scope_order');
        });
    }

    public function down(): void
    {
        if (DB::table('website_contacts')->whereNotNull('website_address_id')->exists()) {
            throw new RuntimeException('Preserve and reconcile address-specific contacts before reversing this migration.');
        }
        Schema::table('website_contacts', function (Blueprint $table): void {
            $table->dropForeign(DB::getDriverName() === 'sqlite' ? ['website_setting_id', 'website_address_id'] : 'website_contacts_address_owner');
            $table->dropUnique('website_contacts_unique_value');
            $table->dropUnique('website_contacts_unique_primary');
            $table->dropIndex('website_contacts_scope_order');
            $table->dropColumn(['scope_key', 'website_address_id']);
            $table->unique(['website_setting_id', 'type', 'normalized_value'], 'website_contacts_unique_value');
            $table->unique(['website_setting_id', 'primary_type'], 'website_contacts_unique_primary');
        });
    }
};
