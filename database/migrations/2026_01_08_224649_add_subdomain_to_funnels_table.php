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
        Schema::table('funnels', function (Blueprint $table) {
            // Subdomain unique pour chaque funnel (ex: mon-produit.votreapp.com)
            $table->string('subdomain', 63)->nullable()->unique()->after('slug');

            // Domaine personnalisé complet (ex: www.monsite.com) - Futur Premium Feature
            $table->string('custom_domain')->nullable()->after('subdomain');

            // SSL actif pour le domaine personnalisé
            $table->boolean('ssl_active')->default(false)->after('custom_domain');

            // Date de vérification du domaine
            $table->timestamp('domain_verified_at')->nullable()->after('ssl_active');

            // Index pour recherche rapide par subdomain
            $table->index('subdomain');
            $table->index('custom_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funnels', function (Blueprint $table) {
            $table->dropIndex(['subdomain']);
            $table->dropIndex(['custom_domain']);
            $table->dropColumn(['subdomain', 'custom_domain', 'ssl_active', 'domain_verified_at']);
        });
    }
};
