<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\nbt\LittleEndianNbtSerializer;

$dbPath = __DIR__ . '/../server/worlds/world/db';
$db = new \LevelDB($dbPath);

$it = new \LevelDBIterator($db);
$it->seekToFirst();

$tilesCount = 0;
$entitiesCount = 0;
$keys = [];

while ($it->valid()) {
    $key = $it->key();
    $len = strlen($key);
    if ($len >= 9) {
        $tag = ord($key[8]);
        // '1' (0x31) = Entity, '2' (0x32) = Pending ticks, '3' (0x33) = Block entity
        if ($tag === 0x31) {
            $entitiesCount++;
        } elseif ($tag === 0x32) {
            // block entity (tile)
            $tilesCount++;
        }
    }
    $it->next();
}

echo "Total entities stored in LevelDB: $entitiesCount\n";
echo "Total tiles stored in LevelDB: $tilesCount\n";
