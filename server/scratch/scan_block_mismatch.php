<?php

$exe = 'C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/Minecraft.Windows.exe';
$f = fopen($exe, 'rb');
$bufferSize = 10 * 1024 * 1024;
$offset = 0;
$target = "BlockMismatch";
$targetLen = strlen($target);

while (!feof($f)) {
    $chunk = fread($f, $bufferSize);
    $pos = strpos($chunk, $target);
    if ($pos !== false) {
        $realPos = $offset + $pos;
        echo "Found '$target' at offset $realPos\n";
        // Print surrounding 200 bytes as printable characters
        $surround = substr($chunk, max(0, $pos - 150), 300);
        $clean = preg_replace('/[^\x20-\x7E]/', '.', $surround);
        echo "Context: $clean\n";
    }
    $offset += strlen($chunk) - $targetLen;
    fseek($f, $offset);
}
fclose($f);
echo "Scan complete.\n";
