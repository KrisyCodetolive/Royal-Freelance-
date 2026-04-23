<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('funnels', function (Blueprint $table) {
            $table->boolean('is_template')->default(false)->after('status');
            $table->string('template_category')->nullable()->after('is_template');
            $table->text('template_description')->nullable()->after('template_category');
            $table->string('template_thumbnail')->nullable()->after('template_description');
            $table->json('template_tags')->nullable()->after('template_thumbnail');
            $table->unsignedInteger('template_uses_count')->default(0)->after('template_tags');

            $table->index('is_template');
            $table->index('template_category');
        });
    }

    public function down(): void
    {
        Schema::table('funnels', function (Blueprint $table) {
            $table->dropIndex(['is_template']);
            $table->dropIndex(['template_category']);
            $table->dropColumn([
                'is_template',
                'template_category',
                'template_description',
                'template_thumbnail',
                'template_tags',
                'template_uses_count',
            ]);
        });
    }
};
