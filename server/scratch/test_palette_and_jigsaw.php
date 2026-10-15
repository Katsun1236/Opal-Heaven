<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\nbt\tag\CompoundTag;

$start = microtime(true);
$tc = TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

$blockPalette = [];
foreach($states as $state){
    $stateData = $state->generateStateData();
    $statesTag = CompoundTag::create();
    foreach($stateData->getStates() as $k => $v){
        $statesTag->setTag($k, $v);
    }
    $blockPalette[] = new BlockPaletteEntry($stateData->getName(), new CacheableNbt($statesTag));
}

echo "Block palette entries generated: " . count($blockPalette) . " in " . round((microtime(true) - $start) * 1000, 2) . " ms\n";

$cache = StaticPacketCache::getInstance();
echo "StaticPacketCache loaded. Biomes: " . count($cache->getBiomeDefs()->getDefinitionData()) . "\n";
echo "Jigsaw packet NBT bytes: " . strlen($cache->getJigsawStructureData()->getNbt()->getEncodedNbt()) . "\n";
