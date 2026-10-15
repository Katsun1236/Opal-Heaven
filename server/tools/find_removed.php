<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$raw = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$palette = BlockStateDictionary::loadPaletteFromString($raw);

$found = [];
foreach ($palette as $i => $e) {
    if (str_contains($e->getName(), 'sulfur') || str_contains($e->getName(), 'cinnabar')) {
        $found[$e->getName()][] = $i;
    }
}

foreach ($found as $name => $indices) {
    echo "$name : index " . min($indices) . " to " . max($indices) . " (" . count($indices) . " variants)\n";
}
