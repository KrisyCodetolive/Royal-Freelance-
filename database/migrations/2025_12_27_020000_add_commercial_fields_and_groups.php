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
        // Add commercial-specific fields to users
        Schema::table('users', function (Blueprint $table) {
            // Commercial profile
            $table->string('shop_name')->nullable()->after('name')->comment('Nom de boutique du commercial');
            $table->string('subdomain')->nullable()->unique()->after('shop_name')->comment('Sous-domaine personnel du commercial');
            $table->text('bio')->nullable()->after('subdomain');
            $table->string('whatsapp_number')->nullable()->after('bio');
            $table->json('social_links')->nullable()->after('whatsapp_number')->comment('Facebook, Instagram, TikTok, etc.');
            $table->json('branding')->nullable()->after('social_links')->comment('Logo, couleurs personnalisées');

            $table->index('subdomain');
        });

        // Commercial groups - allows admin to group commercials and assign funnels
        Schema::create('commercial_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        // Pivot: Users in commercial groups
        Schema::create('commercial_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['commercial_group_id', 'user_id']);
        });

        // Pivot: Funnels available to commercial groups
        Schema::create('commercial_group_funnel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_customize')->default(false)->comment('Can commercial customize branding');
            $table->timestamps();

            $table->unique(['commercial_group_id', 'funnel_id']);
        });

        // Track which user is using which funnel (for subdomain routing)
        Schema::create('funnel_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('custom_slug')->nullable()->comment('Custom URL slug for this user');
            $table->json('custom_branding')->nullable()->comment('User-specific branding overrides');
            $table->boolean('is_active')->default(true);
            $table->integer('leads_count')->default(0);
            $table->integer('conversions_count')->default(0);
            $table->timestamps();

            $table->unique(['funnel_id', 'user_id']);
            $table->unique(['user_id', 'custom_slug']);
        });

        // Add user_id to leads to track which commercial brought them
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('brought_by')->nullable()->after('assigned_to')
                ->constrained('users')->nullOnDelete()
                ->comment('Commercial who brought this lead via their subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['brought_by']);
            $table->dropColumn('brought_by');
        });

        Schema::dropIfExists('funnel_user');
        Schema::dropIfExists('commercial_group_funnel');
        Schema::dropIfExists('commercial_group_user');
        Schema::dropIfExists('commercial_groups');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['subdomain']);
            $table->dropColumn([
                'shop_name',
                'subdomain',
                'bio',
                'whatsapp_number',
                'social_links',
                'branding',
            ]);
        });
    }
};
