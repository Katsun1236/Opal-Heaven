<?php

$exe = 'C:/XboxGames/Minecraft for Windows/Content/Minecraft.Windows.exe';
if (!file_exists($exe)) {
    echo "EXE not found\n";
    exit(1);
}

// Search for "?read@LevelChunkPacket@@" or "?write@LevelChunkPacket@@" or "LevelChunkPacket"
$f = fopen($exe, 'rb');
$size = filesize($exe);
echo "Exe size: $size bytes\n";

$target = "LevelChunkPacket";
$bufferSize = 20 * 1024 * 1024;
$offset = 0;
while (!feof($f)) {
    $data = fread($f, $bufferSize);
    $pos = 0;
    while (($pos = strpos($data, $target, $pos)) !== false) {
        $realPos = $offset + $pos;
        $context = substr($data, max(0, $pos - 40), 100);
        $clean = preg_replace('/[^\x20-\x7E]/', '.', $context);
        echo "Found at $realPos: $clean\n";
        $pos += strlen($target);
    }
    $offset += strlen($data) - 1000;
    fseek($f, $offset);
}
fclose($f);
