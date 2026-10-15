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
foreach ($entries as $e) {
    fseek($f, $offset);
    $jsonStr = fread($f, $e['len']);
    $offset += $e['len'];
    $json = json_decode($jsonStr, true);
    $id = $json['minecraft:biome']['description']['identifier'] ?? $e['name'];
    $tags = $json['minecraft:biome']['components']['minecraft:tags']['tags'] ?? [];
    echo "$id: " . implode(', ', $tags) . "\n";
}
fclose($f);
