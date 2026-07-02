<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Stub en attendant le Module 2 (Billing & Abonnements) : permet d'attribuer
     * un plan à la création d'un tenant sans dépendre d'un modèle Plan/Subscription.
     * À remplacer par la relation vers Subscription->Plan quand le Module 2 sera construit.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan_slug')->default('free')->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('plan_slug');
        });
    }
};
