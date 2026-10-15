<?php
require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$rawNbt = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$metaJson = Filesystem::fileGetContents(BedrockDataFiles::BLOCK_STATE_META_MAP_JSON);

$metaMap = json_decode($metaJson, true);
$states = BlockStateDictionary::loadPaletteFromString($rawNbt);

echo "Total initial states: " . count($states) . "\n";
echo "Total initial meta entries: " . count($metaMap) . "\n";

$filteredStates = [];
$filteredMeta = [];
$removedCount = 0;
$removedBlocks = [];

foreach ($states as $i => $state) {
    $name = $state->getName();
    if (str_contains($name, 'sulfur') || str_contains($name, 'cinnabar')) {
        $removedCount++;
        $removedBlocks[$name] = ($removedBlocks[$name] ?? 0) + 1;
        continue;
    }
    $filteredStates[] = $state;
    $filteredMeta[] = $metaMap[$i] ?? 0;
}

echo "Filtered states count: " . count($filteredStates) . "\n";
echo "Removed states count: $removedCount\n";
echo "Distinct removed block types: " . count($removedBlocks) . "\n";
