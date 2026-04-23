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
        Schema::table('leads', function (Blueprint $table) {
            // Device & Browser info
            $table->string('device_type')->nullable()->after('user_agent')->comment('mobile, tablet, desktop');
            $table->string('browser')->nullable()->after('device_type');
            $table->string('browser_version')->nullable()->after('browser');
            $table->string('os')->nullable()->after('browser_version');
            $table->string('os_version')->nullable()->after('os');
            
            // Geolocation enrichment
            $table->string('timezone')->nullable()->after('city');
            $table->string('region')->nullable()->after('timezone')->comment('State/Province');
            $table->decimal('latitude', 10, 7)->nullable()->after('region');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            
            // Screen resolution (from first visit)
            $table->string('screen_resolution')->nullable()->after('longitude');
            $table->string('language')->nullable()->after('screen_resolution')->comment('Browser language');
        });

        Schema::table('events', function (Blueprint $table) {
            // Device info at event time
            $table->string('device_type')->nullable()->after('user_agent')->comment('mobile, tablet, desktop');
            $table->string('browser')->nullable()->after('device_type');
            $table->string('os')->nullable()->after('browser');
            
            // Time spent tracking
            $table->integer('time_spent_seconds')->nullable()->after('data')->comment('Time spent on page before this event');
            $table->integer('scroll_depth_percentage')->nullable()->after('time_spent_seconds')->comment('Max scroll depth reached');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'device_type', 'browser', 'browser_version', 'os', 'os_version',
                'timezone', 'region', 'latitude', 'longitude',
                'screen_resolution', 'language'
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'device_type', 'browser', 'os',
                'time_spent_seconds', 'scroll_depth_percentage'
            ]);
        });
    }
};
