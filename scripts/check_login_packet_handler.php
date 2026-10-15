<?php
$file = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/src/network/mcpe/handler/LoginPacketHandler.php';
$content = file_get_contents($file);
if (preg_match_all('/.*Protocol.*/i', $content, $m)) {
    print_r($m[0]);
} else {
    echo "No protocol check in LoginPacketHandler!\n";
}
