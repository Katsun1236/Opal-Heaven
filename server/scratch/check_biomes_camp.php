<?php

$data = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);
echo 'Total biomes in biome_definitions.json: ' . count($data) . "\n";
$camp = 0;
foreach ($data as $name => $d) {
    if (isset($d['components']['minecraft:tags']['tags'])) {
        if (in_array('has_structure_abandoned_camp', $d['components']['minecraft:tags']['tags'])) {
            $camp++;
            echo "  $name has abandoned camp\n";
        }
    }
}
echo 'has_structure_abandoned_camp count: ' . $camp . "\n";
