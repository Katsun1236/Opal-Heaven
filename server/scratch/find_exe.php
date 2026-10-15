<?php
$path = 'C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/Minecraft.Windows.exe';
$f = fopen($path, 'rb');
if (!$f) {
    die("Cannot open exe\n");
}
$overlap = 500;
$prev = "";
$offset = 0;
while (!feof($f)) {
    $chunk = fread($f, 10 * 1024 * 1024);
    $buf = $prev . $chunk;
    $pos = 0;
    while (($pos = strpos($buf, "WorldTransfer", $pos)) !== false) {
        $realPos = $offset + $pos - strlen($prev);
        $context = substr($buf, max(0, $pos - 40), 120);
        // Clean non-printable chars
        $contextClean = preg_replace('/[^\x20-\x7E]/', '.', $context);
        echo "Found at $realPos: $contextClean\n";
        $pos += 13;
    }
    $prev = substr($chunk, -$overlap);
    $offset += strlen($chunk);
}
fclose($f);
