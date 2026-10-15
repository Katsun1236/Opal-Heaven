<?php

$itemTags = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/item_tags.json'), true);
foreach ($itemTags as $k => $arr) {
    if (is_array($arr)) {
        foreach ($arr as $item) {
            if (str_contains($item, "sulfur") || str_contains($item, "cinnabar")) {
                echo "item_tags.json tag $k still contains: $item\n";
            }
        }
    }
}

$r16 = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/r16_to_current_item_map.json'), true);
foreach ($r16 as $k => $arr) {
    if (is_array($arr)) {
        foreach ($arr as $itemKey => $itemVal) {
            if (str_contains($itemKey, "sulfur") || str_contains($itemKey, "cinnabar")) {
                echo "r16_to_current_item_map.json contains: $itemKey\n";
            }
        }
    }
}
