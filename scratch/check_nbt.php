<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;

$data = @zlib_decode(file_get_contents(__DIR__ . '/../server/players/katsun1246.dat'));
$serializer = new BigEndianNbtSerializer();
$tag = $serializer->read($data)->mustGetCompoundTag();

echo "Pos: ";
foreach ($tag->getListTag('Pos')->getValue() as $val) {
    echo $val->getValue() . " ";
}
echo "\n";
echo "Level: " . $tag->getString("Level", "none") . "\n";
