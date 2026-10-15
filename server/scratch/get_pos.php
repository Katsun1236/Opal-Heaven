<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;

$file = 'players/katsun1246.dat';
$raw = gzdecode(file_get_contents($file));
$serializer = new BigEndianNbtSerializer();
$tree = $serializer->read($raw)->mustGetCompoundTag();

$pos = $tree->getListTag('Pos');
echo "X: " . $pos->get(0)->getValue() . "\n";
echo "Y: " . $pos->get(1)->getValue() . "\n";
echo "Z: " . $pos->get(2)->getValue() . "\n";
echo "Level: " . $tree->getString('Level') . "\n";
