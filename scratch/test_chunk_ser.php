<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\Chunk;
use pocketmine\world\generator\opal\OpalGenerator;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pmmp\encoding\ByteBufferWriter;
use pocketmine\network\mcpe\protocol\PacketBatch;

// Testons la sérialisation exacte
$chunk = new Chunk([], false);
$gen = new OpalGenerator(12345, "");

class TestDummyManager implements \pocketmine\world\ChunkManager {
    public function __construct(private Chunk $c) {}
    public function getChunk(int $chunkX, int $chunkZ) : ?Chunk { return $this->c; }
    public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk) : void {}
    public function getBlockStateIdAt(int $x, int $y, int $z) : int { return $this->c->getBlockStateId($x & 0xf, $y, $z & 0xf); }
    public function setBlockStateIdAt(int $x, int $y, int $z, int $block) : void { $this->c->setBlockStateId($x & 0xf, $y, $z & 0xf, $block); }
    public function isInWorld(int $x, int $y, int $z) : bool { return $y >= 0 && $y < 256; }
    public function getMinY() : int { return 0; }
    public function getMaxY() : int { return 256; }
    public function getBlockAt(int $x, int $y, int $z) : \pocketmine\block\Block { return \pocketmine\block\VanillaBlocks::AIR(); }
    public function setBlockAt(int $x, int $y, int $z, \pocketmine\block\Block $block) : void {}
}

$mgr = new TestDummyManager($chunk);
$gen->generateChunk($mgr, 16, 16);
$gen->populateChunk($mgr, 16, 16);

$converter = TypeConverter::getInstance();
$payload = ChunkSerializer::serializeFullChunk($chunk, DimensionIds::OVERWORLD, $converter->getBlockTranslator(), "");
$subCount = ChunkSerializer::getSubChunkCount($chunk, DimensionIds::OVERWORLD);

echo "SubChunkCount: $subCount\n";
echo "Payload length: " . strlen($payload) . " bytes\n";

$pk = LevelChunkPacket::create(new ChunkPosition(16, 16), DimensionIds::OVERWORLD, $subCount, false, null, $payload);
$writer = new ByteBufferWriter();
$pk->encode($writer);
echo "LevelChunkPacket encoded length: " . strlen($writer->getData()) . " bytes\n";
