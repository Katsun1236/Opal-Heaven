<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\world\format\Chunk;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\ByteBufferReader;

$chunk = new Chunk([], false);
for ($x = 0; $x < 16; $x++) {
    for ($z = 0; $z < 16; $z++) {
        $chunk->setBlockStateId($x, 64, $z, 20481500); // Some block state
    }
}

echo "Chunk (16, 16) loaded successfully.\n";
$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();

$subCount = ChunkSerializer::getSubChunkCount($chunk, DimensionIds::OVERWORLD);
echo "SubChunkCount: $subCount\n";

$payload = ChunkSerializer::serializeFullChunk($chunk, DimensionIds::OVERWORLD, $blockTranslator);
echo "Full chunk payload size: " . strlen($payload) . " bytes\n";

$pk = LevelChunkPacket::create(
    new ChunkPosition(16, 16),
    DimensionIds::OVERWORLD,
    $subCount,
    false,
    null,
    $payload
);

$writer = new ByteBufferWriter();
$pk->encode($writer);
$data = $writer->getData();
echo "Encoded LevelChunkPacket size: " . strlen($data) . " bytes\n";

$reader = new ByteBufferReader($data);
$decoded = new LevelChunkPacket();
$decoded->decode($reader);

echo "Decoded subChunkCount: " . $decoded->getSubChunkCount() . "\n";
echo "Decoded extraPayload size: " . strlen($decoded->getExtraPayload()) . "\n";
echo "Decoded position: (" . $decoded->getChunkPosition()->getX() . ", " . $decoded->getChunkPosition()->getZ() . ")\n";

if (strlen($decoded->getExtraPayload()) === strlen($payload)) {
    echo "SUCCESS: Payload sizes match perfectly!\n";
} else {
    echo "ERROR: Payload size mismatch!\n";
}
