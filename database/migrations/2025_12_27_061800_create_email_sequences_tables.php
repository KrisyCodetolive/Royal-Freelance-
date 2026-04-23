<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Email sequences table
        Schema::create('email_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funnel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger'); // EmailSequenceTrigger enum
            $table->string('status')->default('draft'); // EmailSequenceStatus enum
            $table->json('trigger_conditions')->nullable();
            $table->json('settings')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['trigger', 'status']);
        });

        // Email sequence emails table
        Schema::create('email_sequence_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_sequence_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('content');
            $table->integer('send_after_hours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();

            $table->index(['email_sequence_id', 'is_active']);
            $table->index('send_after_hours');
        });

        // Email sequence subscriptions table
        Schema::create('email_sequence_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_sequence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['email_sequence_id', 'lead_id']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['lead_id', 'is_active']);
        });

        // Email sequence email sends table
        Schema::create('email_sequence_email_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('email_sequence_subscriptions')->cascadeOnDelete();
            $table->foreignId('email_sequence_email_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('content');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('tracking_data')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'sent_at']);
            $table->index(['lead_id', 'sent_at']);
            $table->index(['email_sequence_email_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequence_email_sends');
        Schema::dropIfExists('email_sequence_subscriptions');
        Schema::dropIfExists('email_sequence_emails');
        Schema::dropIfExists('email_sequences');
    }
};
