<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\utils\Filesystem;

$raw = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$palette = BlockStateDictionary::loadPaletteFromString($raw);

$spawnBlockNames = [
    "minecraft:air",
    "minecraft:bedrock",
    "minecraft:deepslate",
    "minecraft:emerald_ore",
    "minecraft:stone",
    "minecraft:diamond_ore",
    "minecraft:podzol",
    "minecraft:warped_nylium",
    "minecraft:flowing_water",
    "minecraft:water",
    "minecraft:netherrack",
    "minecraft:crying_obsidian",
    "minecraft:amethyst_block",
    "minecraft:sea_lantern",
    "minecraft:grass_block",
    "minecraft:dirt",
    "minecraft:magma",
    "minecraft:blackstone",
    "minecraft:ancient_debris",
    "minecraft:calcite"
];

$positions = [];
foreach ($palette as $idx => $entry) {
    $name = $entry->getName();
    if (in_array($name, $spawnBlockNames, true)) {
        if (!isset($positions[$name])) {
            $positions[$name] = ["first" => $idx, "count" => 1, "states" => [$entry->getStates()]];
        } else {
            $positions[$name]["count"]++;
        }
    }
}

foreach ($positions as $name => $info) {
    echo sprintf("%-30s : first index = %5d (total variants = %d)\n", $name, $info['first'], $info['count']);
}
