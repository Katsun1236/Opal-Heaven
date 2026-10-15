<?php
$f1 = json_decode(file_get_contents(__DIR__ . '/../server/pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);
$f2 = json_decode(file_get_contents(__DIR__ . '/../server/pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json.bak_pre_12652'), true);

echo "Current biomes count: " . count($f1) . "\n";
echo "Bak biomes count: " . count($f2) . "\n";

$diff1 = array_diff_key($f1, $f2);
echo "Added in current: " . implode(", ", array_keys($diff1)) . "\n";

$diff2 = array_diff_key($f2, $f1);
echo "Removed from current: " . implode(", ", array_keys($diff2)) . "\n";

// Check if tags or values differ for plains
echo "\nPlains current tags: " . json_encode($f1['minecraft:plains']['tags'] ?? []) . "\n";
echo "Plains bak tags: " . json_encode($f2['minecraft:plains']['tags'] ?? []) . "\n";
