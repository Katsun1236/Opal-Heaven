<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

$levelDatPath = __DIR__ . '/../server/worlds/world/level.dat';
$levelDat = file_get_contents($levelDatPath);
$header = substr($levelDat, 0, 8);
$nbtData = substr($levelDat, 8);

$serializer = new LittleEndianNbtSerializer();
$tag = $serializer->read($nbtData)->mustGetCompoundTag();

$tag->setInt("SpawnX", 256);
$tag->setInt("SpawnY", 86);
$tag->setInt("SpawnZ", 256);

$newNbt = $serializer->write(new TreeRoot($tag));
file_put_contents($levelDatPath, $header . $newNbt);

echo "Spawn mis a jour dans level.dat: (256, 86, 256)\n";
