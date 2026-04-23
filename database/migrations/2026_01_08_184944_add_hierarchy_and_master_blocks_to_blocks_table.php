<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Ajoute les colonnes pour :
     * - Hiérarchie parent/enfant des blocs (comme Systeme.io)
     * - Master Blocks réutilisables
     * - Colonnes de mise en page
     */
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            // ============================================
            // HIÉRARCHIE PARENT/ENFANT
            // ============================================
            // Permet de créer des blocs imbriqués (Section > Row > Éléments)
            $table->foreignId('parent_id')
                ->nullable()
                ->after('page_id')
                ->constrained('blocks')
                ->nullOnDelete();

            // Position dans la hiérarchie (depth level)
            $table->unsignedTinyInteger('depth')
                ->default(0)
                ->after('parent_id');

            // ============================================
            // MASTER BLOCKS
            // ============================================
            // Le bloc est-il un master block (template réutilisable)
            $table->boolean('is_master_block')
                ->default(false)
                ->after('is_active');

            // Nom du master block (pour identification)
            $table->string('master_block_name')
                ->nullable()
                ->after('is_master_block');

            // Référence au master block original (si ce bloc est une instance)
            $table->foreignId('master_block_id')
                ->nullable()
                ->after('master_block_name')
                ->constrained('blocks')
                ->nullOnDelete();

            // ============================================
            // LAYOUT / COLONNES
            // ============================================
            // Configuration des colonnes (pour type COLUMNS)
            // Ex: "1/2,1/2" pour 2 colonnes égales, "1/3,2/3" pour 1/3 + 2/3
            $table->string('columns_layout')
                ->nullable()
                ->after('styles');

            // Index pour la colonne (0, 1, 2, 3)
            $table->unsignedTinyInteger('column_index')
                ->nullable()
                ->after('columns_layout');

            // ============================================
            // STYLES AVANCÉS
            // ============================================
            // Styles responsive pour mobile
            $table->json('mobile_styles')
                ->nullable()
                ->after('styles');

            // Animations d'entrée
            $table->string('animation')
                ->nullable()
                ->after('mobile_styles');

            $table->string('animation_delay')
                ->nullable()
                ->after('animation');

            // ============================================
            // VISIBILITÉ ET CONDITIONS
            // ============================================
            // Conditions d'affichage (ex: afficher si quiz_score > 50)
            $table->json('display_conditions')
                ->nullable()
                ->after('show_on_desktop');

            // ============================================
            // INDEXES
            // ============================================
            $table->index('parent_id');
            $table->index('master_block_id');
            $table->index('is_master_block');
            $table->index(['page_id', 'parent_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['master_block_id']);

            // Drop indexes
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['master_block_id']);
            $table->dropIndex(['is_master_block']);
            $table->dropIndex(['page_id', 'parent_id', 'sort_order']);

            // Drop columns
            $table->dropColumn([
                'parent_id',
                'depth',
                'is_master_block',
                'master_block_name',
                'master_block_id',
                'columns_layout',
                'column_index',
                'mobile_styles',
                'animation',
                'animation_delay',
                'display_conditions',
            ]);
        });
    }
};
