<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreePrinter;

$file = 'players/katsun1246.dat';
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $decomp = @gzdecode($raw);
    if ($decomp !== false) $raw = $decomp;
    $serializer = new BigEndianNbtSerializer();
    $tree = $serializer->read($raw);
    echo "=== CONTENU DE players/katsun1246.dat ===\n";
    print_r($tree->getTag()->getValue());
} else {
    echo "Fichier katsun1246.dat introuvable\n";
}
