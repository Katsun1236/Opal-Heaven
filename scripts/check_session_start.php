<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$handlerFile = 'phar://' . $pharFile . '/src/network/mcpe/handler/SessionStartPacketHandler.php';
$lines = explode("\n", file_get_contents($handlerFile));
foreach (array_slice($lines, 25, 45, true) as $i => $l) {
    echo ($i + 1) . ": " . $l . "\n";
}
