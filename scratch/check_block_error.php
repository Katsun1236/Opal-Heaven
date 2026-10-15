<?php
$json = json_decode(file_get_contents('C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/data/definitions/disconnection_errors/disconnection_error_messaging.json'), true);

foreach($json['Themes'] as $t){
    echo "Theme: " . $t['ThemeName'] . " | Codeword: " . ($t['DefaultStrings']['Codeword'] ?? 'none') . " | Errors count: " . count($t['Errors'] ?? []) . "\n";
}
