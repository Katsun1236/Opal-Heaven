<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\World;

$logger = new class extends \pocketmine\thread\log\AttachableThreadSafeLogger {
    public function emergency($m): void {}
    public function alert($m): void {}
    public function critical($m): void {}
    public function error($m): void {}
    public function warning($m): void {}
    public function notice($m): void {}
    public function info($m): void {}
    public function debug($m): void {}
    public function log($l, $m): void {}
    public function logException(\Throwable $e, $t = null): void {}
};
$path = 'server/worlds/world_backup';
$provider = new LevelDB($path, $logger);



echo "Loaded world provider: " . get_class($provider) . PHP_EOL;

$loaded = $provider->loadChunk(16, 16);
if ($loaded === null) {
    echo "Chunk (16, 16) is NULL in provider!" . PHP_EOL;
} else {
    $chunk = $loaded->getData();
    echo "Chunk (16, 16) loaded successfully!" . PHP_EOL;
    echo "Subchunks count: " . count($chunk->getSubChunks()) . PHP_EOL;
    echo "Tiles count: " . count($chunk->getTileNBT()) . PHP_EOL;
    foreach ($chunk->getTileNBT() as $tile) {
        echo "Tile NBT: " . $tile->toString() . PHP_EOL;
    }
    echo "Entities count: " . count($chunk->getEntityNBT()) . PHP_EOL;
    foreach ($chunk->getEntityNBT() as $ent) {
        echo "Entity NBT: " . $ent->toString() . PHP_EOL;
    }


    
    $biomes = [];
    foreach ($chunk->getSubChunks() as $y => $sub) {
        foreach ($sub->getBiomeArray()->getPalette() as $b) {
            $biomes[$b] = true;
        }
    }
    echo "Biomes in chunk: " . implode(', ', array_keys($biomes)) . PHP_EOL;
}

