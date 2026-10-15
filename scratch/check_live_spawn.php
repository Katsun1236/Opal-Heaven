<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\LittleEndianNbtSerializer;

$levelDat = file_get_contents(__DIR__ . '/../server/worlds/world/level.dat');
$nbt = (new LittleEndianNbtSerializer())->read(substr($levelDat, 8))->mustGetCompoundTag();

echo "Current Spawn in level.dat: (" . $nbt->getInt("SpawnX") . ", " . $nbt->getInt("SpawnY") . ", " . $nbt->getInt("SpawnZ") . ")\n";
