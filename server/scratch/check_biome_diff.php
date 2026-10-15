<?php

$file = 'C:/XboxGames/Minecraft for Windows/Content/data/behavior_packs/vanilla_1.26.50/__brarchive/biomes.brarchive';
$f = fopen($file, 'rb');
fseek($f, 8);
$numEntries = unpack('V', fread($f, 4))[1];

$entries = [];
for ($i = 0; $i < $numEntries; $i++) {
    fseek($f, 16 + ($i * 256));
    $nameLen = ord(fread($f, 1));
    $name = fread($f, $nameLen);
    fseek($f, 16 + ($i * 256) + 252);
    $len = unpack('V', fread($f, 4))[1];
    $entries[] = ['name' => $name, 'len' => $len];
}

$offset = 16 + ($numEntries * 256);
$serverDefs = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);

foreach ($entries as $e) {
    fseek($f, $offset);
    $jsonStr = fread($f, $e['len']);
    $offset += $e['len'];
    $clientBiome = json_decode($jsonStr, true);
    $id = $clientBiome['minecraft:biome']['description']['identifier'];
    
    if (!isset($serverDefs[$id])) {
        echo "[MISSING IN SERVER] $id\n";
    } else {
        $serverTags = $serverDefs[$id]['components']['minecraft:tags']['tags'] ?? [];
        $clientTags = $clientBiome['minecraft:biome']['components']['minecraft:tags']['tags'] ?? [];
        $diff = array_diff($clientTags, $serverTags);
        if (!empty($diff)) {
            echo "[$id] Missing tags in server: " . implode(', ', $diff) . "\n";
        }
    }
}
fclose($f);
