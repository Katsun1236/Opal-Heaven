<?php

require 'pmmp_src/vendor/autoload.php';
use pocketmine\network\mcpe\cache\StaticPacketCache;

$pk = StaticPacketCache::getInstance()->getJigsawStructureData();
$root = $pk->getNbt()->getRoot();
echo "Jigsaw packet NBT root tag count: " . $root->count() . "\n";
foreach ($root as $k => $v) {
    echo " - $k (" . get_class($v) . ")\n";
}
