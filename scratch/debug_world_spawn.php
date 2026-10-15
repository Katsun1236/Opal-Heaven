<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\utils\MainLogger;
use pocketmine\block\Block;

$logger = new MainLogger(null, false, "Debug", new \DateTimeZone("UTC"));
$provider = new LevelDB(__DIR__ . '/../server/worlds/world', $logger);

$spawn = $provider->getWorldData()->getSpawn();
echo "Provider getWorldData()->getSpawn(): " . $spawn->x . ", " . $spawn->y . ", " . $spawn->z . "\n";

$chunk = $provider->loadChunk(16, 16);
if ($chunk === null) {
    echo "Chunk (16, 16) is NULL in provider!\n";
    exit;
}

echo "Inspecting chunk (16, 16) in LevelDB directly:\n";
$highest = 0;
for ($y = 0; $y < 256; $y++) {
    $state = $chunk->getBlockStateId(0, $y, 0);
    if ($state !== Block::EMPTY_STATE_ID) {
        $highest = $y;
    }
}
echo "Highest block at x=0, z=0 in chunk (16,16) is Y=$highest\n";
for ($y = 65; $y <= 90; $y++) {
    $state = $chunk->getBlockStateId(0, $y, 0);
    echo "  Y=$y: stateId=" . ($state === Block::EMPTY_STATE_ID ? "AIR" : $state) . "\n";
}
