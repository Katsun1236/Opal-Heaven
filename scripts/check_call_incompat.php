<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$sessionFile = 'phar://' . $pharFile . '/src/network/mcpe/NetworkSession.php';
$lines = explode("\n", file_get_contents($sessionFile));
foreach ($lines as $i => $line) {
    if (strpos($line, 'disconnectIncompatibleProtocol') !== false && $i < 850) {
        for ($j = max(0, $i - 5); $j <= min(count($lines) - 1, $i + 15); $j++) {
            echo ($j + 1) . ": " . $lines[$j] . "\n";
        }
    }
}
