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
        Schema::table('funnel_user', function (Blueprint $table) {
            $table->boolean('can_edit')->default(false)->after('is_active')
                ->comment('Module 4 - partage : accès édition (Filament) au tunnel, sinon lecture seule');
        });

        Schema::table('commercial_group_funnel', function (Blueprint $table) {
            $table->boolean('can_edit')->default(false)->after('can_customize')
                ->comment('Module 4 - partage : accès édition (Filament) au tunnel pour les membres du groupe, sinon lecture seule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funnel_user', function (Blueprint $table) {
            $table->dropColumn('can_edit');
        });

        Schema::table('commercial_group_funnel', function (Blueprint $table) {
            $table->dropColumn('can_edit');
        });
    }
};
