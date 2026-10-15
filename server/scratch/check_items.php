<?php
require 'pmmp_src/vendor/autoload.php';

$json = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/required_item_list.json');
$items = json_decode($json, true);
echo "Total items in required_item_list.json: " . count($items) . PHP_EOL;

// Check if cinnabar or sulfur items are in required_item_list.json
foreach($items as $name => $id) {
    if (str_contains($name, 'cinnabar') || str_contains($name, 'sulfur')) {
        echo "Found deprecated item: $name -> $id\n";
    }
}

// Check bundle or recent 1.26 items
$foundBundle = false;
foreach($items as $name => $id) {
    if (str_contains($name, 'bundle')) {
        $foundBundle = true;
        echo "Found bundle item: $name -> $id\n";
    }
}
echo "Bundle found: " . ($foundBundle ? "YES" : "NO") . PHP_EOL;
