<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\BlockStateDictionary;

$origNbt = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/canonical_block_states.nbt.bak_original');
$origMeta = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/block_state_meta_map.json.bak_original');

$dict = BlockStateDictionary::loadFromString($origNbt, $origMeta);
echo "Original states count: " . count($dict->getStates()) . "\n";

// Let's find Network IDs of air, stone, dirt, grass_block in original
foreach ($dict->getStates() as $id => $entry) {
    if ($entry->getStateName() === 'minecraft:air') {
        echo "Original minecraft:air -> Network ID: $id\n";
        break;
    }
}
foreach ($dict->getStates() as $id => $entry) {
    if ($entry->getStateName() === 'minecraft:stone') {
        echo "Original minecraft:stone -> Network ID: $id\n";
        break;
    }
}
foreach ($dict->getStates() as $id => $entry) {
    if ($entry->getStateName() === 'minecraft:dirt') {
        echo "Original minecraft:dirt -> Network ID: $id\n";
        break;
    }
}
foreach ($dict->getStates() as $id => $entry) {
    if ($entry->getStateName() === 'minecraft:grass_block') {
        echo "Original minecraft:grass_block -> Network ID: $id\n";
        break;
    }
}
