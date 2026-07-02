<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price_monthly')->default(0);
            $table->unsignedInteger('price_yearly')->default(0);
            // null = illimité
            $table->unsignedInteger('max_tunnels')->nullable();
            $table->unsignedInteger('max_mailing_lists')->nullable();
            $table->unsignedInteger('max_leads')->nullable();
            $table->unsignedInteger('max_shared_tunnels')->nullable();
            $table->json('available_roles')->nullable();
            $table->string('dashboard_level')->default('standard');
            $table->string('support_level')->default('communautaire');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
