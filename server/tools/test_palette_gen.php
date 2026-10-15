<?php
require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\nbt\tag\CompoundTag;

$start = microtime(true);
$tc = TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

echo "Nombre total d'etats de blocs dans le dictionnaire: " . count($states) . "\n";

$entries = [];
foreach ($states as $state) {
    $stateData = $state->generateStateData();
    $statesTag = CompoundTag::create();
    foreach ($stateData->getStates() as $k => $v) {
        $statesTag->setTag($k, $v);
    }
    $entries[] = new BlockPaletteEntry($stateData->getName(), new CacheableNbt($statesTag));
}

$elapsed = microtime(true) - $start;
echo sprintf("Generation de %d BlockPaletteEntry en %.2f ms\n", count($entries), $elapsed * 1000);
