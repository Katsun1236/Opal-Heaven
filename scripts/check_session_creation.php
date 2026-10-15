<?php
$sessionFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/src/network/mcpe/NetworkSession.php';
$lines = explode("\n", file_get_contents($sessionFile));
foreach ($lines as $i => $line) {
    if (strpos($line, 'SessionStartPacketHandler') !== false) {
        for ($j = max(0, $i - 5); $j <= min(count($lines) - 1, $i + 20); $j++) {
            echo ($j + 1) . ": " . $lines[$j] . "\n";
        }
    }
}
