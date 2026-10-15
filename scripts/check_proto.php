<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$protoFile = 'phar://' . $pharFile . '/vendor/pocketmine/bedrock-protocol/src/ProtocolInfo.php';
$content = file_get_contents($protoFile);
if (preg_match_all('/const [A-Z0-9_]+ = [^;]+;/', $content, $m)) {
    print_r(array_slice($m[0], 0, 15));
}
