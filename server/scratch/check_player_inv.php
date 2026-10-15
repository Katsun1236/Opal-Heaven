<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;

$file = 'players/katsun1246.dat';
$raw = gzdecode(file_get_contents($file));
$serializer = new BigEndianNbtSerializer();
$tree = $serializer->read($raw)->mustGetCompoundTag();

echo "Pos: " . json_encode($tree->getListTag('Pos')?->getValue()) . "\n";
echo "Inventory items count: " . $tree->getListTag('Inventory')?->count() . "\n";
foreach ($tree->getListTag('Inventory') ?? [] as $itemTag) {
    echo "Item: " . $itemTag->getString('id') . ", count=" . $itemTag->getByte('Count') . "\n";
}
