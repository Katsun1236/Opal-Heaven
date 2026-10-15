<?php

$file = 'C:/XboxGames/Minecraft for Windows/Content/data/behavior_packs/vanilla_1.26.50/__brarchive/blocks.brarchive';
if (!file_exists($file)) {
    echo "File not found\n";
    exit(1);
}

$f = fopen($file, 'rb');
fseek($f, 8);
$num = unpack('V', fread($f, 4))[1];
echo "Num entries in blocks.brarchive: $num\n";

for ($i = 0; $i < $num; $i++) {
    fseek($f, 16 + ($i * 256));
    $len = ord(fread($f, 1));
    $name = fread($f, $len);
    echo "  $i: $name\n";
}
fclose($f);
