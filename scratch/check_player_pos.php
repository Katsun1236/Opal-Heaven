<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

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
$provider = new \pocketmine\world\format\io\leveldb\LevelDB(__DIR__ . '/../server/worlds/world_backup', $logger);
$loaded = $provider->loadChunk(16, 16);
if ($loaded !== null) {
    $chunk = $loaded->getData();
    // In chunk 16, 16: world X=256 -> chunk x = 256 & 15 = 0. world Z=256 -> chunk z = 0.
    echo "Blocks at column (x=0, z=0) from Y=65 to 90:" . PHP_EOL;
    for ($y = 65; $y <= 90; $y++) {
        $subIndex = $y >> 4;
        $sub = $chunk->getSubChunks()[$subIndex] ?? null;
        if ($sub !== null) {
            $stateId = $sub->getBlockLayers()[0]->get(0, $y & 15, 0);
            try {
                $block = \pocketmine\block\RuntimeBlockStateRegistry::getInstance()->fromStateId($stateId);
                $name = $block->getName();
            } catch (\Throwable $e) {
                $name = "UNKNOWN: " . $e->getMessage();
            }

            echo "Y=$y: stateId=$stateId ($name)" . PHP_EOL;
        }

    }
}
