<?php
$file = 'C:/Program Files/WindowsApps/Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe/Minecraft.Windows.exe';
$f = @fopen($file, 'rb');
if(!$f){
    echo "Cannot open exe directly: " . (error_get_last()['message'] ?? 'Permission denied') . "\n";
    exit;
}
echo "Opened successfully!\n";
$target = "InitialConnection";
$buffer = "";
$offset = 0;
while(!feof($f)){
    $chunk = fread($f, 1024 * 1024);
    $combined = $buffer . $chunk;
    $pos = strpos($combined, $target);
    if($pos !== false){
        $actualOffset = $offset - strlen($buffer) + $pos;
        echo "Found target at offset $actualOffset!\n";
        $surrounding = substr($combined, max(0, $pos - 200), 500);
        echo "Surrounding: " . preg_replace('/[^\x20-\x7e\n\r]/', '.', $surrounding) . "\n";
    }
    $buffer = substr($chunk, -strlen($target));
    $offset += strlen($chunk);
}

fclose($f);
