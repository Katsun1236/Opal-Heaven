<?php
require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$nbtPath = BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT;
$metaPath = BedrockDataFiles::BLOCK_STATE_META_MAP_JSON;

// 1. Backups
$backupNbt = $nbtPath . ".bak_original";
$backupMeta = $metaPath . ".bak_original";

if (!file_exists($backupNbt)) {
    copy($nbtPath, $backupNbt);
    echo "Backup cree: $backupNbt\n";
}
if (!file_exists($backupMeta)) {
    copy($metaPath, $backupMeta);
    echo "Backup cree: $backupMeta\n";
}

$rawNbt = Filesystem::fileGetContents($backupNbt);
$metaJson = Filesystem::fileGetContents($backupMeta);

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

$serializer = new NetworkNbtSerializer();
$serialized = "";
foreach ($filteredStates as $s) {
    $serialized .= $serializer->write(new TreeRoot($s->toNbt()));
}

Filesystem::safeFilePutContents($nbtPath, $serialized);
Filesystem::safeFilePutContents($metaPath, json_encode($filteredMeta));

echo sprintf("Palette nettoyee appliquee avec succes ! (%d etats conserves, 1067 sulfur/cinnabar retires)\n", count($filteredStates));
