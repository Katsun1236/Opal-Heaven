<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';
require_once __DIR__ . '/../server/plugins/OpalCore/src/opalheaven/core/biomes/OpalBiome.php';
require_once __DIR__ . '/../server/plugins/OpalCore/src/opalheaven/core/biomes/BiomeRegistry.php';

use opalheaven\core\biomes\BiomeRegistry;

$dir = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugin_data/OpalCore/biomes/';
$reg = new BiomeRegistry($dir);

echo "Loaded biomes count: " . $reg->getCount() . "\n";
foreach ($reg->getAll() as $id => $biome) {
    echo "  [OK] " . $id . " -> " . $biome->getName() . " (Category: " . $biome->getCategory() . ")\n";
}
