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
            // CTA Configuration for each commercial's funnel
            $table->string('shop_url')->nullable()->after('custom_branding')
                ->comment('URL boutique pour redirection finale');
            $table->string('whatsapp_redirect')->nullable()->after('shop_url')
                ->comment('Numéro WhatsApp pour le CTA');
            $table->text('whatsapp_message')->nullable()->after('whatsapp_redirect')
                ->comment('Message WhatsApp pré-rempli');

            // Analytics
            $table->integer('views_count')->default(0)->after('conversions_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funnel_user', function (Blueprint $table) {
            $table->dropColumn([
                'shop_url',
                'whatsapp_redirect',
                'whatsapp_message',
                'views_count',
            ]);
        });
    }
};
