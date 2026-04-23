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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color')->default('#6366f1');
            $table->text('description')->nullable();
            $table->boolean('is_auto')->default(false)->comment('Automatically assigned tag');
            $table->string('auto_trigger')->nullable()->comment('Event type that triggers this tag');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'is_auto']);
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Contact info
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // UTM tracking
            $table->string('source')->nullable()->comment('utm_source');
            $table->string('medium')->nullable()->comment('utm_medium');
            $table->string('campaign')->nullable()->comment('utm_campaign');
            $table->string('referrer')->nullable();

            // Scoring
            $table->integer('score')->default(0);
            $table->string('status')->default('cold'); // LeadStatus enum

            // Custom data
            $table->json('custom_fields')->nullable();
            $table->json('form_data')->nullable()->comment('Original form submission data');

            // Tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            // Timestamps
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'funnel_id']);
            $table->index(['tenant_id', 'score']);
            $table->index(['tenant_id', 'assigned_to']);
            $table->index('email');
            $table->index('phone');
            $table->index(['funnel_id', 'created_at']);
        });

        Schema::create('lead_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['lead_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_tag');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('tags');
    }
};
