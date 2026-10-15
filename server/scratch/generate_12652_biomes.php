<?php

$dirs = glob('C:/XboxGames/Minecraft for Windows/Content/data/behavior_packs/*/__brarchive/biomes.brarchive');
echo "Found " . count($dirs) . " biomes.brarchive files\n";

$allBiomes = [];
foreach ($dirs as $file) {
    preg_match('#behavior_packs/([^/]+)/#', $file, $m);
    $packName = $m[1] ?? 'unknown';
    
    $f = fopen($file, 'rb');
    fseek($f, 8);
    $num = unpack('V', fread($f, 4))[1];
    
    $entries = [];
    for ($i = 0; $i < $num; $i++) {
        fseek($f, 16 + ($i * 256));
        $len = ord(fread($f, 1));
        $name = fread($f, $len);
        fseek($f, 16 + ($i * 256) + 252);
        $dLen = unpack('V', fread($f, 4))[1];
        $entries[] = ['name' => $name, 'len' => $dLen];
    }
    
    $offset = 16 + ($num * 256);
    foreach ($entries as $e) {
        fseek($f, $offset);
        $content = fread($f, $e['len']);
        $offset += $e['len'];
        $json = json_decode($content, true);
        if ($json && isset($json['minecraft:biome']['description']['identifier'])) {
            $id = $json['minecraft:biome']['description']['identifier'];
            // Later behavior packs override earlier ones (e.g. vanilla_1.26.50 overrides vanilla)
            $allBiomes[$id] = $json['minecraft:biome'];
        }
    }
    fclose($f);
}

// Read current server biome_definitions.json as base
$serverDefs = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);

$updated = $serverDefs;

foreach ($allBiomes as $id => $clientBiome) {
    $clientTags = $clientBiome['components']['minecraft:tags']['tags'] ?? [];
    
    if (isset($updated[$id])) {
        // Merge tags
        $existingTags = $updated[$id]['tags'] ?? [];
        $mergedTags = array_values(array_unique(array_merge($existingTags, $clientTags)));
        $updated[$id]['tags'] = $mergedTags;
    } else {
        echo "Adding missing biome to server: $id\n";
        $climate = $clientBiome['components']['minecraft:climate'] ?? [];
        $downfall = $climate['downfall'] ?? 0.5;
        $temp = $climate['temperature'] ?? 0.5;
        $updated[$id] = [
            'depth' => 0.1,
            'downfall' => $downfall,
            'foliageSnow' => 0,
            'id' => 65535,
            'mapWaterColour' => ['a' => 165, 'b' => 206, 'g' => 119, 'r' => 6],
            'rain' => ($downfall > 0),
            'scale' => 0.2,
            'tags' => array_values(array_unique($clientTags)),
            'temperature' => $temp
        ];
    }
}

// Check has_structure_abandoned_camp count
$camp = 0;
foreach ($updated as $id => $b) {
    if (in_array('has_structure_abandoned_camp', $b['tags'])) {
        $camp++;
    }
}

echo "Total biomes: " . count($updated) . "\n";
echo "Biomes with has_structure_abandoned_camp: $camp\n";

// Save test output
file_put_contents('scratch/updated_biome_definitions.json', json_encode($updated, JSON_PRETTY_PRINT));
echo "Saved to scratch/updated_biome_definitions.json\n";
