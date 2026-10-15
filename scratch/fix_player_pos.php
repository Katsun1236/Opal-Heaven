<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\utils\Filesystem;

$file = 'server/players/katsun1246.dat';
if(!file_exists($file)){
    echo "File not found\n";
    exit;
}
$raw = Filesystem::fileGetContents($file);
$decompressed = @zlib_decode($raw);
if($decompressed === false) $decompressed = $raw;

$serializer = new BigEndianNbtSerializer();
$nbt = $serializer->read($decompressed)->mustGetCompoundTag();

// Mettre le joueur à Y=73.0 (au-dessus du sol à Y=71)
$posList = new ListTag([
    new DoubleTag(256.0),
    new DoubleTag(73.0),
    new DoubleTag(256.0)
]);
$nbt->setTag("Pos", $posList);

// FallDistance = 0, Motion = 0
$nbt->setFloat("FallDistance", 0.0);
$nbt->setTag("Motion", new ListTag([
    new DoubleTag(0.0),
    new DoubleTag(0.0),
    new DoubleTag(0.0)
]));

$contents = zlib_encode($serializer->write(new TreeRoot($nbt)), ZLIB_ENCODING_GZIP);
file_put_contents($file, $contents);
echo "Updated katsun1246.dat successfully! Position set to (256, 73, 256)\n";
