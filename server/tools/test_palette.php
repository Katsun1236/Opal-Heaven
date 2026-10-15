<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$raw = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$palette = BlockStateDictionary::loadPaletteFromString($raw);
echo "Total canonical block states in server: " . count($palette) . "\n";
echo "First 5 states:\n";
for ($i = 0; $i < 5; $i++) {
    echo "  #$i: " . $palette[$i]->getName() . " -> " . json_encode($palette[$i]->getStates()) . "\n";
}
