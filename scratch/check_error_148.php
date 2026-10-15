<?php
$json = json_decode(file_get_contents("C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/data/definitions/disconnection_errors/disconnection_error_messaging.json"), true);

$allErrors = [];
$idx = 0;
foreach($json["Themes"] as $theme){
    $themeName = $theme["ThemeName"];
    $codeword = $theme["DefaultStrings"]["Codeword"] ?? "N/A";
    foreach($theme["Errors"] ?? [] as $err){
        $allErrors[] = [
            'index' => $idx,
            'theme' => $themeName,
            'codeword' => $err['OverrideStrings']['Codeword'] ?? $codeword,
            'failReason' => $err['FailReason'],
            'raw' => $err
        ];
        $idx++;
    }
}

echo "Total errors found: " . count($allErrors) . "\n";
if(isset($allErrors[148])){
    echo "=== Error at Index 148 ===\n";
    print_r($allErrors[148]);
}
if(isset($allErrors[147])){
    echo "=== Error at Index 147 (0-based) ===\n";
    print_r($allErrors[147]);
}

foreach($allErrors as $e){
    if($e['codeword'] === 'Boat'){
        echo "Boat error ({$e['index']}): {$e['failReason']}\n";
    }
}
