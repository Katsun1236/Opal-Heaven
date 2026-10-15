<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;

$logger = new class extends \SimpleLogger {
    public function log($level, $message) {}
};

$db = new LevelDB('server/worlds/world_backup', $logger);
$chunkData = $db->loadChunk(16, 16);
$subChunks = $chunkData->getData()->getSubChunks();

echo "Subchunks in chunk (16, 16):\n";
foreach($subChunks as $y => $sub) {
    if(!$sub->isEmptyFast()) {
        $minY = ($y * 16);
        $maxY = $minY + 15;
        echo "Subchunk $y (Y=$minY to $maxY) is NOT empty\n";
    }
}

echo "\nBlocks at column X=256, Z=256 (offset x=0, z=0 within chunk):\n";
for($y = 100; $y >= 60; $y--) {
    $subIndex = $y >> 4;
    $sub = $subChunks[$subIndex] ?? null;
    if($sub !== null) {
        $state = $sub->getBlockStateId(0, $y & 0x0f, 0);
        if($state !== 0) {
            echo "Y=$y: stateId=$state\n";
        }
    }
}
