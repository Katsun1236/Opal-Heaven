<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\nbt\tag\CompoundTag;

$tc = TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

$blockPaletteEntries = [];
foreach ($states as $state) {
    $stateData = $state->generateStateData();
    $statesTag = CompoundTag::create();
    foreach ($stateData->getStates() as $k => $v) {
        $statesTag->setTag($k, $v);
    }
    $blockPaletteEntries[] = new BlockPaletteEntry($stateData->getName(), new CacheableNbt($statesTag));
}

echo "Created " . count($blockPaletteEntries) . " block palette entries.\n";
echo "First entry: " . $blockPaletteEntries[0]->getName() . "\n";
echo "Done.\n";
