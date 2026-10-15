<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\world\format\io\leveldb\LevelDB;

$path = __DIR__ . '/../server/worlds/world';
$db = new LevelDB($path);
$chunk = $db->loadChunk(16, 16);

if ($chunk === null) {
    echo "Chunk (16, 16) is not in database!\n";
    exit;
}

echo "Chunk (16, 16) found in DB. Checking blocks at local x=0, z=0 (world x=256, z=256):\n";
for ($y = 65; $y <= 95; $y++) {
    $stateId = $chunk->getBlockStateId(0, $y, 0);
    echo "Y=$y: stateId=$stateId\n";
}
