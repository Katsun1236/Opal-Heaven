<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\block\RuntimeBlockStateRegistry;

$block = RuntimeBlockStateRegistry::getInstance()->fromStateId(20481500);
echo "Block name: " . $block->getName() . "\n";
echo "Block class: " . get_class($block) . "\n";
