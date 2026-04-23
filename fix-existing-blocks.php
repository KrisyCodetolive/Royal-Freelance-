<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Block;

echo "🔧 Réparation des blocs existants\n\n";

// Récupérer tous les blocs qui n'ont pas show_on_mobile/desktop définis
$blocks = Block::whereNull('show_on_mobile')
    ->orWhereNull('show_on_desktop')
    ->orWhere('show_on_mobile', false)
    ->orWhere('show_on_desktop', false)
    ->get();

echo "Blocs à réparer : {$blocks->count()}\n\n";

$fixed = 0;
foreach ($blocks as $block) {
    echo "Réparation Block #{$block->id} (type: {$block->type->value})...\n";
    
    $block->update([
        'show_on_mobile' => true,
        'show_on_desktop' => true,
    ]);
    
    $fixed++;
}

echo "\n✅ {$fixed} blocs réparés!\n";
echo "\nRafraîchissez maintenant votre page dans le builder.\n";
