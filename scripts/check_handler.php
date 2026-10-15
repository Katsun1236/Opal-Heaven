<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$handlerFile = 'phar://' . $pharFile . '/src/network/mcpe/handler/LoginPacketHandler.php';
if (file_exists($handlerFile)) {
    $content = file_get_contents($handlerFile);
    $lines = explode("\n", $content);
    foreach ($lines as $i => $line) {
        if (strpos($line, 'disconnectIncompatibleProtocol') !== false) {
            for ($j = max(0, $i - 5); $j <= min(count($lines) - 1, $i + 10); $j++) {
                echo ($j + 1) . ": " . $lines[$j] . "\n";
            }
        }
    }
}
