<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * plan_slug était un stub temporaire (Module 1) en attendant le vrai
     * modèle Plan/Subscription (Module 2). Remplacé par Tenant::currentPlan().
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('plan_slug');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan_slug')->default('free')->after('locale');
        });
    }
};
