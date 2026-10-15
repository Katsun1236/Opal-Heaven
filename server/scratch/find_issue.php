<?php
$f = file('C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/data/resource_packs/vanilla/texts/en_US.lang');
foreach ($f as $line) {
    if (stripos($line, 'Primary') !== false || stripos($line, 'SubClient') !== false) {
        echo $line;
    }
}










