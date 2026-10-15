<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;

$path = __DIR__ . '/../server/worlds/world_backup';
$logger = new \SimpleLogger();
$db = new LevelDB($path, $logger);

$loadedChunkData = $db->loadChunk(16, 16);
if ($loadedChunkData === null) {
    echo "Chunk (16, 16) is NULL in database!\n";
    exit(1);
}
$chunkData = $loadedChunkData->getData();
$chunk = new \pocketmine\world\format\Chunk($chunkData->getSubChunks(), $chunkData->isPopulated());

echo "=== CHUNK (16, 16) ANALYSIS ===\n";
echo "Loaded successfully.\n";

$tc = TypeConverter::getInstance();
$bt = $tc->getBlockTranslator();

$subCount = ChunkSerializer::getSubChunkCount($chunk, DimensionIds::OVERWORLD);
echo "SubChunkCount: $subCount\n";

for ($y = -4; $y <= 19; $y++) {
    $sub = $chunk->getSubChunk($y);
    $empty = $sub->isEmptyFast() ? "EMPTY" : "CONTAINS BLOCKS";
    echo "  Subchunk $y (Y=" . ($y * 16) . ".." . ($y * 16 + 15) . "): $empty\n";
}

echo "\nBlocks at (X=256, Z=256) which is local (0, 0) in chunk (16, 16):\n";
for ($y = 60; $y <= 95; $y++) {
    $stateId = $chunk->getBlockStateId(0, $y, 0);
    $block = \pocketmine\block\RuntimeBlockStateRegistry::getInstance()->fromStateId($stateId);
    $name = $block->getName();
    echo "  Y=$y: $name (StateId: $stateId)\n";
}
