<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Funnel;
use App\Models\Page;
use App\Enums\PageType;
use App\Services\TemplateGeneratorService;

echo "🔍 Test de la logique de récupération des blocs\n\n";

// 1. Créer un funnel de test
echo "1️⃣ Création d'un funnel de test...\n";
$funnel = Funnel::firstOrCreate(
    ['slug' => 'test-funnel'],
    [
        'name' => 'Test Funnel',
        'tenant_id' => 1,
        'is_active' => true,
    ]
);
echo "✅ Funnel créé: #{$funnel->id} - {$funnel->name}\n\n";

// 2. Créer une page avec template
echo "2️⃣ Création d'une page avec template 'capture_split'...\n";
$page = Page::create([
    'funnel_id' => $funnel->id,
    'title' => 'Page de Test',
    'slug' => 'test-page-' . time(),
    'type' => PageType::CAPTURE,
    'sort_order' => 1,
    'is_active' => true,
    'is_required' => true,
]);
echo "✅ Page créée: #{$page->id} - {$page->title}\n\n";

// 3. Appliquer le template
echo "3️⃣ Application du template...\n";
$service = new TemplateGeneratorService();
$service->applyPageTemplate($page, 'capture_split');
echo "✅ Template appliqué\n\n";

// 4. Vérifier les blocs créés
echo "4️⃣ Vérification des blocs créés:\n";
$allBlocks = $page->blocks()->get();
echo "Total blocs bruts: {$allBlocks->count()}\n";
foreach($allBlocks as $block) {
    echo "  - Block #{$block->id}: {$block->type->value}\n";
    echo "    * is_active: " . ($block->is_active ? 'OUI' : 'NON') . "\n";
    echo "    * show_on_mobile: " . ($block->show_on_mobile ? 'OUI' : 'NON') . "\n";
    echo "    * show_on_desktop: " . ($block->show_on_desktop ? 'OUI' : 'NON') . "\n";
    echo "    * sort_order: {$block->sort_order}\n";
}
echo "\n";

// 5. Tester le scope active()
echo "5️⃣ Test du scope active():\n";
$activeBlocks = $page->blocks()->active()->get();
echo "Blocs actifs: {$activeBlocks->count()}\n";
foreach($activeBlocks as $block) {
    echo "  - Block #{$block->id}: {$block->type->value}\n";
}
echo "\n";

// 6. Tester le scope ordered()
echo "6️⃣ Test du scope ordered():\n";
$orderedBlocks = $page->blocks()->active()->ordered()->get();
echo "Blocs actifs et ordonnés: {$orderedBlocks->count()}\n";
foreach($orderedBlocks as $block) {
    echo "  - Block #{$block->id}: {$block->type->value} (order: {$block->sort_order})\n";
}
echo "\n";

// 7. Tester comme dans PageBuilder computed
echo "7️⃣ Test comme PageBuilder->blocks():\n";
$builderBlocks = $page->blocks()->active()->ordered()->get();
echo "Résultat PageBuilder: {$builderBlocks->count()} blocs\n";
foreach($builderBlocks as $block) {
    echo "  - Block #{$block->id}: {$block->type->value}\n";
}
echo "\n";

echo "✅ URL du builder: /page-builder/Page/{$page->id}\n";
echo "\n🎉 Test terminé!\n";
