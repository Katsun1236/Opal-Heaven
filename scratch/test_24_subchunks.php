<?php
require 'server/pmmp_src/vendor/autoload.php';

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
        $chunk->setBlockStateId($x, 64, $z, 20481500);
    }
}

$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();

// Test with 24 subchunks
$stream = new ByteBufferWriter();
[$minY, $maxY] = ChunkSerializer::getDimensionChunkBounds(DimensionIds::OVERWORLD);
for ($y = $minY; $y <= $maxY; $y++) {
    ChunkSerializer::serializeSubChunk($chunk->getSubChunk($y), $blockTranslator, $stream, false);
}

// Biomes
$biomeIdMap = \pocketmine\data\bedrock\LegacyBiomeIdToStringIdMap::getInstance();
for ($y = $minY; $y <= $maxY; $y++) {
    $ref = new ReflectionClass(ChunkSerializer::class);
    $m = $ref->getMethod("serializeBiomePalette");
    $m->setAccessible(true);
    $m->invoke(null, $chunk->getSubChunk($y)->getBiomeArray(), $biomeIdMap, $stream);
}

// Border blocks
\pmmp\encoding\Byte::writeUnsigned($stream, 0);

// Tiles
$payload = $stream->getData();
echo "Payload size for 24 subchunks: " . strlen($payload) . " bytes\n";

$pk = LevelChunkPacket::create(
    new ChunkPosition(16, 16),
    DimensionIds::OVERWORLD,
    24,
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
echo "Match: " . (strlen($decoded->getExtraPayload()) === strlen($payload) ? "YES" : "NO") . "\n";
