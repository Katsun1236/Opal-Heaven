<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\network\mcpe\convert\TypeConverter;

class DummyLogger extends \pmmp\thread\ThreadSafe implements \Logger {
    public function emergency($m): void {}
    public function alert($m): void {}
    public function critical($m): void {}
    public function error($m): void {}
    public function warning($m): void {}
    public function notice($m): void {}
    public function info($m): void {}
    public function debug($m): void {}
    public function log($l, $m): void {}
    public function logException(\Throwable $e, $trace = null): void {}
}

$worldPath = 'worlds/world';
$provider = new LevelDB($worldPath, new DummyLogger());
$chunk = $provider->loadChunk(16, 16);

if ($chunk === null) {
    echo "Chunk (16, 16) is null!\n";
    exit(1);
}

echo "Chunk (16, 16) loaded successfully!\n";
$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();

$payload = ChunkSerializer::serializeFullChunk($chunk, DimensionIds::OVERWORLD, $blockTranslator);
echo "Full chunk payload length: " . strlen($payload) . " bytes\n";

[$minY, $maxY] = ChunkSerializer::getDimensionChunkBounds(DimensionIds::OVERWORLD);
for ($y = $minY; $y <= $maxY; $y++) {
    $sub = $chunk->getSubChunk($y);
    $layers = $sub->getBlockLayers();
    $palette0 = $layers[0]->getPalette();
    if (count($palette0) > 1 || $palette0[0] !== 0) {
        echo "Subchunk Y=$y has " . count($palette0) . " palette blocks: ";
        foreach (array_slice($palette0, 0, 4) as $internalId) {
            $netId = $blockTranslator->internalIdToNetworkId($internalId);
            $stateData = $blockTranslator->internalIdToNetworkStateData($internalId);
            echo "[int=$internalId, net=$netId, name=" . $stateData->getName() . "] ";
        }
        echo "\n";
    }
}
