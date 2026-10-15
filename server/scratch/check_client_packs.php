<?php

$dir = 'C:/Users/basti/AppData/Roaming/Minecraft Bedrock/Users/4542480576725733784/games/com.mojang';
$subdirs = scandir($dir);
print_r($subdirs);

foreach (['behavior_packs', 'resource_packs', 'development_resource_packs', 'development_behavior_packs'] as $p) {
    $full = "$dir/$p";
    if (is_dir($full)) {
        echo "Found dir: $p\n";
        print_r(scandir($full));
    }
}
