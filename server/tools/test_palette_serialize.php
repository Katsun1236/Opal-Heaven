<?php
require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$rawNbt = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$metaJson = Filesystem::fileGetContents(BedrockDataFiles::BLOCK_STATE_META_MAP_JSON);

$metaMap = json_decode($metaJson, true);
$states = BlockStateDictionary::loadPaletteFromString($rawNbt);

$filteredStates = [];
$filteredMeta = [];
foreach ($states as $i => $state) {
    $name = $state->getName();
    if (str_contains($name, 'sulfur') || str_contains($name, 'cinnabar')) {
        continue;
    }
    $filteredStates[] = $state;
    $filteredMeta[] = $metaMap[$i] ?? 0;
}

// Sérialisons
$serializer = new NetworkNbtSerializer();
$serialized = "";
foreach ($filteredStates as $s) {
    $serialized .= $serializer->write(new TreeRoot($s->toNbt()));
}

echo "Taille sérialisée: " . strlen($serialized) . " octets\n";

// Relisons pour vérifier
$reloaded = BlockStateDictionary::loadPaletteFromString($serialized);
echo "Rechargé avec succès: " . count($reloaded) . " états !\n";

// Testons chargement BlockStateDictionary
$dict = BlockStateDictionary::loadFromString($serialized, json_encode($filteredMeta));
echo "BlockStateDictionary créé avec succès: " . count($dict->getStates()) . " entrées !\n";
