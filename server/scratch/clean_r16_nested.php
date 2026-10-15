<?php

$f = 'pmmp_src/vendor/pocketmine/bedrock-data/r16_to_current_item_map.json';
$raw = file_get_contents($f);
$data = json_decode($raw, true);

if (isset($data['complex'])) {
    foreach ($data['complex'] as $parent => &$sub) {
        if (is_array($sub)) {
            foreach ($sub as $meta => $val) {
                if (str_contains($val, 'sulfur') || str_contains($val, 'cinnabar')) {
                    echo "Removed complex sub: $parent -> $meta => $val\n";
                    unset($sub[$meta]);
                }
            }
        }
    }
}

file_put_contents($f, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "r16 cleaned successfully\n";
