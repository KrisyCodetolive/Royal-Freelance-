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
        if (!Schema::hasColumn('funnel_user', 'custom_slug')) {
            Schema::table('funnel_user', function (Blueprint $table) {
                $table->string('custom_slug')->nullable()->after('custom_branding')
                    ->comment('Slug personnalisé du commercial pour ce tunnel');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funnel_user', function (Blueprint $table) {
            $table->dropColumn('custom_slug');
        });
    }
};
