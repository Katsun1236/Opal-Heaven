<?php

$file = 'C:/XboxGames/Minecraft for Windows/Content/data/behavior_packs/vanilla_1.26.50/__brarchive/biomes.brarchive';
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
    if (strpos($e['name'], 'dappled_forest') !== false) {
        echo "=== " . $e['name'] . " ===\n";
        echo $content . "\n";
    }
}
fclose($f);
