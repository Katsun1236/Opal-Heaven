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
            $allBiomes[$id] = [
                'pack' => $packName,
                'data' => $json['minecraft:biome']
            ];
        }
    }
    fclose($f);
}

echo "Total unique biomes found in client behavior packs: " . count($allBiomes) . "\n";
ksort($allBiomes);
foreach (array_keys($allBiomes) as $id) {
    echo "  $id (" . $allBiomes[$id]['pack'] . ")\n";
}
