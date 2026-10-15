<?php

$files = [
    'pmmp_src/vendor/pocketmine/bedrock-data/required_item_list.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/block_id_to_item_id_map.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/block_properties_table.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/creative/construction.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/creative/items.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/creative/nature.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/biome_id_map.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/entity_id_map.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/item_tags.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/r16_to_current_item_map.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/recipes/shaped_crafting.json',
    'pmmp_src/vendor/pocketmine/bedrock-data/recipes/shapeless_crafting.json',
];

echo "=== INSPECTION DES STRUCTURES ===\n";
foreach ($files as $f) {
    $raw = file_get_contents($f);
    $data = json_decode($raw, true);
    $isList = array_is_list($data);
    $count = count($data);
    echo "$f: count=$count, isList=" . ($isList ? 'true' : 'false') . "\n";
}
