<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\cache\StaticPacketCache;

$cache = StaticPacketCache::getInstance();
$palette = $cache->getBlockPalette();
echo "getBlockPalette() count: " . count($palette) . "\n";
echo "getBiomeDefs() count: " . count($cache->getBiomeDefs()->getDefinitionData()) . "\n";
echo "getJigsawStructureData() bytes: " . strlen($cache->getJigsawStructureData()->getNbt()->getEncodedNbt()) . "\n";
