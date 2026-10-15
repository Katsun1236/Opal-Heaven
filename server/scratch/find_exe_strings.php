<?php

$exe = 'C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/Minecraft.Windows.exe';
if (!file_exists($exe)) {
    echo "Exe not found: $exe\n";
    exit;
}

$handle = fopen($exe, 'rb');
$size = filesize($exe);
echo "Exe size: $size bytes\n";

// Search for "Boat" or disconnection messages
// Let's search strings with powershell or strings command
