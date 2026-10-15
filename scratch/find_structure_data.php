<?php
$exePath = 'C:\\Program Files\\WindowsApps\\Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe\\Minecraft.Windows.exe';
if (!file_exists($exePath)) {
    echo "EXE not found\n";
    exit;
}
$fp = fopen($exePath, 'rb');
$buffer = '';
$offset = 0;
$found = [];
$chunkSize = 10 * 1024 * 1024; // 10MB
$search = 'MissingStructureData';

while (!feof($fp)) {
    $data = fread($fp, $chunkSize);
    $combined = $buffer . $data;
    $pos = 0;
    while (($pos = strpos($combined, $search, $pos)) !== false) {
        $realPos = $offset - strlen($buffer) + $pos;
        echo "Found '$search' at offset $realPos\n";
        $contextStart = max(0, $pos - 300);
        $context = substr($combined, $contextStart, 600);
        // print printable chars only
        $clean = preg_replace('/[^\x20-\x7E\r\n\t]/', '.', $context);
        echo "Context:\n$clean\n\n";
        $pos += strlen($search);
    }
    $buffer = substr($data, -1000);
    $offset += strlen($data);
}
fclose($fp);
echo "Done search.\n";
