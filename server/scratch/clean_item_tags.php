<?php

$tagFile = 'pmmp_src/vendor/pocketmine/bedrock-data/item_tags.json';
$itemTags = json_decode(file_get_contents($tagFile), true);
$before = count($itemTags);
foreach ($itemTags as $k => $v) {
    if (str_contains($k, 'sulfur') || str_contains($k, 'cinnabar')) {
        unset($itemTags[$k]);
    }
}
file_put_contents($tagFile, json_encode($itemTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "item_tags.json cleaned: $before -> " . count($itemTags) . "\n";
