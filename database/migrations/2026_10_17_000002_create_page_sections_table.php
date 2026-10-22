<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('type', 20);
            $table->unsignedInteger('sort_order');
            $table->boolean('is_enabled')->default(true);
            $table->string('heading', 200);
            $table->text('body')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->restrictOnDelete();
            $table->string('image_alt', 255)->nullable();
            $table->string('button_label', 80)->nullable();
            $table->string('button_url', 2048)->nullable();
            $table->timestamps();
            $table->index(['page_id', 'sort_order', 'id']);
            $table->foreign('page_id')->references('id')->on('pages')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
