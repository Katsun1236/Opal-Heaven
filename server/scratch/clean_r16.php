<?php

$f = 'pmmp_src/vendor/pocketmine/bedrock-data/r16_to_current_item_map.json';
$data = json_decode(file_get_contents($f), true);

foreach ($data as $topK => &$arr) {
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if (str_contains($k, 'sulfur') || str_contains($k, 'cinnabar') || (is_string($v) && (str_contains($v, 'sulfur') || str_contains($v, 'cinnabar')))) {
                echo "Found in $topK: $k => " . json_encode($v) . "\n";
                unset($arr[$k]);
            }
        }
    }
}
unset($arr);

file_put_contents($f, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "r16_to_current_item_map cleaned\n";
