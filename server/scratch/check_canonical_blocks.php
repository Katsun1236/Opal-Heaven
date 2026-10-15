<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\nbt\TreeRoot;

$raw = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/canonical_block_states.nbt');
$decompressed = @gzdecode($raw) ?: $raw;

$trees = (new NetworkNbtSerializer())->readMultiple($decompressed);
echo "Total block states: " . count($trees) . "\n";

$uniqueBlocks = [];
foreach ($trees as $tree) {
    $tag = $tree->mustGetCompoundTag();
    $name = $tag->getString('name');
    $uniqueBlocks[$name] = ($uniqueBlocks[$name] ?? 0) + 1;
}

echo "Total unique block names: " . count($uniqueBlocks) . "\n";
$samples = ['minecraft:air', 'minecraft:stone', 'minecraft:grass_block', 'minecraft:poplar_log', 'minecraft:black_concrete_slab'];
foreach ($samples as $s) {
    echo "  $s: " . ($uniqueBlocks[$s] ?? 'NOT FOUND') . "\n";
}
