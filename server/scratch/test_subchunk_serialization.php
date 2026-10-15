<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\world\format\SubChunk;
use pocketmine\world\format\PalettedBlockArray;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\block\VanillaBlocks;

$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();

$air = VanillaBlocks::AIR()->getStateId();
$sub = new SubChunk($air, [new PalettedBlockArray($air)], new PalettedBlockArray(0));
$sub->setBlockStateId(0, 0, 0, VanillaBlocks::STONE()->getStateId());
$sub->setBlockStateId(0, 1, 0, VanillaBlocks::DIRT()->getStateId());
$sub->setBlockStateId(0, 2, 0, VanillaBlocks::GRASS()->getStateId());

echo "Testing serializeSubChunk on subchunk with Stone, Dirt, Grass:\n";
$stream = new \pmmp\encoding\ByteBufferWriter();
ChunkSerializer::serializeSubChunk($sub, $blockTranslator, $stream, false);
$bytes = $stream->getData();
echo "Encoded subchunk size: " . strlen($bytes) . " bytes\n";

$reader = new \pmmp\encoding\ByteBufferReader($bytes);
$version = \pmmp\encoding\Byte::readUnsigned($reader);
$layersCount = \pmmp\encoding\Byte::readUnsigned($reader);
echo "Subchunk format version: $version, layers: $layersCount\n";
for ($l = 0; $l < $layersCount; $l++) {
    $header = \pmmp\encoding\Byte::readUnsigned($reader);
    $bitsPerBlock = $header >> 1;
    $isNetwork = ($header & 1);
    echo " Layer $l: bitsPerBlock=$bitsPerBlock, isNetwork=$isNetwork (1=network, 0=persistent)\n";
    // read word array
    $wordsCount = (int) ceil((16 * 16 * 16) / (32 / $bitsPerBlock));
    $words = $reader->readByteArray($wordsCount * 4);
    $paletteCount = \pmmp\encoding\VarInt::readSignedInt($reader);
    echo " Palette entries count: $paletteCount\n";
    for ($p = 0; $p < $paletteCount; $p++) {
        $netId = \pmmp\encoding\VarInt::readSignedInt($reader);
        $stateData = $blockTranslator->getBlockStateDictionary()->generateDataFromStateId($netId);
        echo "   Palette[$p] -> Network ID: $netId, Name: " . ($stateData?->getName() ?? 'NULL') . "\n";
    }
}
