<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\ByteBufferReader;

$pk = LevelChunkPacket::create(
    new ChunkPosition(16, 16),
    DimensionIds::OVERWORLD,
    5,
    false,
    null,
    "FAKE_TERRAIN_PAYLOAD_TEST_DATA"
);

$writer = new ByteBufferWriter();
$pk->encode($writer);
$data = $writer->getData();

echo "Encoded packet size: " . strlen($data) . " bytes\n";
echo "Packet hex: " . bin2hex($data) . "\n";

$reader = new ByteBufferReader($data);
$decoded = new LevelChunkPacket();
$decoded->decode($reader);

echo "Decoded chunk pos: (" . $decoded->getChunkPosition()->getX() . ", " . $decoded->getChunkPosition()->getZ() . ")\n";
echo "Decoded dimension: " . $decoded->getDimensionId() . "\n";
echo "Decoded subChunkCount: " . $decoded->getSubChunkCount() . "\n";
echo "Decoded extraPayload: " . $decoded->getExtraPayload() . "\n";
echo "SUCCESS!\n";
