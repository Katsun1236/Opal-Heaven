<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$sessionFile = 'phar://' . $pharFile . '/src/network/mcpe/NetworkSession.php';
$content = file_get_contents($sessionFile);
if (preg_match('/function handleLoginPacket.*?\{.*?\}/s', $content, $m)) {
    echo substr($m[0], 0, 1500);
} else {
    // Search for CURRENT_PROTOCOL in NetworkSession
    if (preg_match_all('/.*CURRENT_PROTOCOL.*/', $content, $m2)) {
        print_r($m2[0]);
    }
}
