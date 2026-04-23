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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('presentation'); // PageType enum
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();

            // Page settings
            $table->json('settings')->nullable()->comment('Page-specific settings');
            $table->json('branding')->nullable()->comment('Override funnel branding');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false)->comment('Must be completed to proceed');

            // Stats cache
            $table->integer('views_count')->default(0);
            $table->integer('submissions_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['funnel_id', 'slug']);
            $table->index(['funnel_id', 'sort_order']);
            $table->index(['funnel_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
