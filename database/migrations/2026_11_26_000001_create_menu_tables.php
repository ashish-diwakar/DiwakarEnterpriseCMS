<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('location', 30)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('lock_version')->default(0);
            $table->timestamps();
        });
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('page_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('label', 200);
            $table->string('type', 20);
            $table->string('url', 2048)->nullable();
            $table->string('target', 10)->default('_self');
            $table->unsignedInteger('sort_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['id', 'menu_id']);
            $table->foreign(['parent_id', 'menu_id'])->references(['id', 'menu_id'])->on('menu_items')->restrictOnDelete();
            $table->index(['menu_id', 'parent_id', 'sort_order', 'id']);
        });
        Schema::create('menu_initializations', function (Blueprint $table): void {
            $table->string('key', 100)->primary();
            $table->timestamp('completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_initializations');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
