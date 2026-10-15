<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\world\format\SubChunk;
use pocketmine\world\format\PalettedBlockArray;
use pocketmine\world\format\Chunk;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\ByteBufferReader;
use pocketmine\network\mcpe\protocol\PacketBatch;

$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();

$air = VanillaBlocks::AIR()->getStateId();
$sub = new SubChunk($air, [new PalettedBlockArray($air)], new PalettedBlockArray(0));
$sub->setBlockStateId(0, 0, 0, VanillaBlocks::STONE()->getStateId());

$chunk = new Chunk([0 => $sub], false);

$payload = ChunkSerializer::serializeFullChunk($chunk, DimensionIds::OVERWORLD, $blockTranslator);
$packet = LevelChunkPacket::create(new ChunkPosition(16, 16), DimensionIds::OVERWORLD, 1, false, null, $payload);

$writer = new ByteBufferWriter();
$packet->encode($writer);
$bytes = $writer->getData();

echo "Encoded LevelChunkPacket size: " . strlen($bytes) . " bytes\n";
echo "First 50 bytes hex: " . bin2hex(substr($bytes, 0, 50)) . "\n";

// Decode it back with LevelChunkPacket::decode
$reader = new ByteBufferReader($bytes);
$decoded = new LevelChunkPacket();
$decoded->decode($reader);

echo "Decoded ChunkPosition: (" . $decoded->getChunkPosition()->getX() . ", " . $decoded->getChunkPosition()->getZ() . ")\n";
echo "Decoded Dimension: " . $decoded->getDimensionId() . "\n";
echo "Decoded SubChunkCount: " . $decoded->getSubChunkCount() . "\n";
echo "Decoded isClientSubChunkRequestsEnabled: " . ($decoded->isClientSubChunkRequestsEnabled() ? "true" : "false") . "\n";
echo "Decoded isCacheEnabled: " . ($decoded->isCacheEnabled() ? "true" : "false") . "\n";
echo "Decoded extraPayload length: " . strlen($decoded->getExtraPayload()) . "\n";
echo "Reader remaining unread bytes: " . $reader->getUnreadLength() . "\n";
