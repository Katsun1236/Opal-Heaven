<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$raw = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$palette = BlockStateDictionary::loadPaletteFromString($raw);

echo "Total states: " . count($palette) . "\n";
echo "First 15 names:\n";
for ($i = 0; $i < 15; $i++) {
    echo "  #$i: " . $palette[$i]->getName() . "\n";
}

// Vérifions si les noms sont ordonnés par un hash ou par ordre alphabétique
$names = [];
foreach ($palette as $i => $e) {
    $names[$e->getName()] = true;
}
$uniqueNames = array_keys($names);
echo "Total unique block names: " . count($uniqueNames) . "\n";
echo "First 10 unique names:\n";
for ($i = 0; $i < 10; $i++) {
    echo "  [" . $i . "] " . $uniqueNames[$i] . "\n";
}
