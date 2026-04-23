<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // BlockType enum

            // Content based on block type
            $table->json('content')->nullable()->comment('Block content data');
            $table->json('settings')->nullable()->comment('Block-specific settings');
            $table->json('styles')->nullable()->comment('Custom CSS styles');

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // Visibility settings
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);

            $table->timestamps();

            $table->index(['page_id', 'sort_order']);
            $table->index(['page_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
