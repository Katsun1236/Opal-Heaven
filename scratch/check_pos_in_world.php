<?php
require 'server/pmmp_src/vendor/autoload.php';
use pocketmine\world\format\io\leveldb\LevelDB;

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
$worldPath = 'server/worlds/world';
$provider = new LevelDB($worldPath, $logger);
$chunk = $provider->loadChunk(16, 16);
if ($chunk !== null) {
    echo "Chunk (16,16) found in world!\n";
    echo "Block at (0, 4, 0) inside chunk (world coords 256, 4, 256): StateId = " . $chunk->getBlockStateId(0, 4, 0) . "\n";
    echo "Block at (0, 70, 0) inside chunk (world coords 256, 70, 256): StateId = " . $chunk->getBlockStateId(0, 70, 0) . "\n";
    for ($y = 319; $y >= -64; $y--) {
        $s = $chunk->getBlockStateId(0, $y, 0);
        if ($s !== 20481500 && $s !== 0) {
            echo "Highest non-air block at Y=$y (StateId=$s)\n";
            break;
        }
    }
} else {
    echo "Chunk (16, 16) not found in world provider!\n";
}
