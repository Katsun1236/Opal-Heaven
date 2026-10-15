<?php

// Check biome_definitions.json
$biomes = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);
foreach ($biomes as $k => $b) {
    $str = json_encode($b);
    if (str_contains($str, 'sulfur') || str_contains($str, 'cinnabar')) {
        echo "Biome match: key=$k, name=" . ($b['name'] ?? 'none') . "\n";
    }
}

// Check entity_identifiers.nbt
$entNbt = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/entity_identifiers.nbt');
if (str_contains($entNbt, 'sulfur') || str_contains($entNbt, 'cinnabar')) {
    echo "Entity match: sulfur_cube found in entity_identifiers.nbt\n";
}

// Check entity_id_map.json
$entities = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/entity_id_map.json'), true);
foreach ($entities as $k => $v) {
    if (str_contains($k, 'sulfur') || str_contains($k, 'cinnabar')) {
        echo "Entity ID map: $k => $v\n";
    }
}
