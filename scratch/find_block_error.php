<?php
$filePath = 'C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/data/definitions/disconnection_errors/disconnection_error_messaging.json';
$raw = file_get_contents($filePath);
$json = json_decode($raw, true);
echo "JSON root keys: " . implode(', ', array_keys($json)) . "\n";
if(isset($json['Themes'])) {
    foreach($json['Themes'] as $t) {
        if(($t['DefaultStrings']['Codeword'] ?? '') === 'Block') {
            echo "Theme codeword Block: " . json_encode($t, JSON_PRETTY_PRINT) . "\n";
        }
        foreach($t['Errors'] ?? [] as $err) {
            if(($err['OverrideStrings']['Codeword'] ?? '') === 'Block' || ($err['FailReason'] ?? '') === 'BlockMismatch') {
                echo "Error: " . json_encode($err, JSON_PRETTY_PRINT) . "\n";
            }
        }
    }
}
