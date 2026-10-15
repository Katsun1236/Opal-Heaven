<?php
$path = 'C:\Program Files\WindowsApps\Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe\Minecraft.Windows.exe';
$f = @fopen($path, 'rb');
var_dump($f !== false);
if ($f) fclose($f);
