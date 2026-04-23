<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des tags
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('color')->default('gray'); // Couleur du badge
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['tenant_id', 'is_active']);
            });

            // Insérer quelques tags par défaut
            DB::table('tags')->insert([
            [
                'tenant_id' => 1,
                'name' => 'VIP',
                'slug' => 'vip',
                'color' => 'warning',
                'description' => 'Lead à forte valeur',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'Relance 7j',
                'slug' => 'relance-7j',
                'color' => 'info',
                'description' => 'À relancer après 7 jours',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'Relance 14j',
                'slug' => 'relance-14j',
                'color' => 'danger',
                'description' => 'Relance urgente après 14 jours',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'Intéressé Formation',
                'slug' => 'interesse-formation',
                'color' => 'success',
                'description' => 'A manifesté un intérêt pour la formation',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'WhatsApp Actif',
                'slug' => 'whatsapp-actif',
                'color' => 'success',
                'description' => 'A cliqué sur WhatsApp',
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'Vidéo Complète',
                'slug' => 'video-complete',
                'color' => 'primary',
                'description' => 'A regardé une vidéo en entier',
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);
        }

        // Table pivot lead_tag
        if (!Schema::hasTable('lead_tag')) {
            Schema::create('lead_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('assigned_at')->useCurrent();
                
                $table->unique(['lead_id', 'tag_id']);
                $table->index('tag_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_tag');
        Schema::dropIfExists('tags');
    }
};
