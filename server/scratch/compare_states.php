<?php

require 'pmmp_src/vendor/autoload.php';
use pocketmine\network\mcpe\convert\BlockStateDictionary;

$nbt1 = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/canonical_block_states.nbt');
$meta1 = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/block_state_meta_map.json');
$d1 = BlockStateDictionary::loadFromString($nbt1, $meta1);

$nbt2 = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/canonical_block_states.nbt.bak_original');
$meta2 = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/block_state_meta_map.json.bak_original');
$d2 = BlockStateDictionary::loadFromString($nbt2, $meta2);

echo "Current count: " . count($d1->getStates()) . "\n";
echo "Bak_original count: " . count($d2->getStates()) . "\n";

$names1 = [];
foreach ($d1->getStates() as $s) {
    $names1[$s->getStateName()] = ($names1[$s->getStateName()] ?? 0) + 1;
}
$names2 = [];
foreach ($d2->getStates() as $s) {
    $names2[$s->getStateName()] = ($names2[$s->getStateName()] ?? 0) + 1;
}

$diff2_not_1 = array_diff_key($names2, $names1);
echo "Block types in bak_original but NOT in current: " . count($diff2_not_1) . "\n";
foreach (array_slice(array_keys($diff2_not_1), 0, 20) as $name) {
    echo " - $name\n";
}

$diff1_not_2 = array_diff_key($names1, $names2);
echo "Block types in current but NOT in bak_original: " . count($diff1_not_2) . "\n";
foreach (array_slice(array_keys($diff1_not_2), 0, 20) as $name) {
    echo " - $name\n";
}
