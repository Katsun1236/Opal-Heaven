<?php

$file = 'pmmp_src/resources/jigsaw_structure_data.nbt';
if (!file_exists($file)) {
    echo "File not found\n";
    exit(1);
}

require 'pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

$raw = file_get_contents($file);
$nbt = (new BigEndianNbtSerializer())->read($raw);
$tag = $nbt->getTag();

echo "Root tag name: " . $tag->getName() . "\n";
foreach ($tag->getValue() as $k => $v) {
    echo "  Key: $k (type " . get_class($v) . ")\n";
}

$sets = $tag->getListTag('structure_sets');
if ($sets) {
    echo "Structure sets count: " . $sets->count() . "\n";
    foreach ($sets as $set) {
        $id = $set->getString('id', 'unknown');
        echo "  Set ID: $id\n";
    }
}

$structures = $tag->getListTag('structures');
if ($structures) {
    echo "Structures count: " . $structures->count() . "\n";
    foreach ($structures as $st) {
        $id = $st->getString('id', 'unknown');
        $biomes = $st->getCompoundTag('biomes');
        $tags = $biomes ? $biomes->getListTag('tags') : null;
        $tagList = [];
        if ($tags) {
            foreach ($tags as $t) $tagList[] = $t->getValue();
        }
        echo "  Structure: $id | biome tags: " . implode(',', $tagList) . "\n";
    }
}
