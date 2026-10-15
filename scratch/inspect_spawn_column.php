<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\world\generator\opal\OpalGenerator;

class DummyManager implements ChunkManager {
    public function __construct(private Chunk $c) {}
    public function getChunk(int $chunkX, int $chunkZ) : ?Chunk { return $this->c; }
    public function setChunk(int $chunkX, int $chunkZ, Chunk $chunk) : void {}
    public function getBlockStateIdAt(int $x, int $y, int $z) : int { return $this->c->getBlockStateId($x & 0xf, $y, $z & 0xf); }
    public function setBlockStateIdAt(int $x, int $y, int $z, int $block) : void { $this->c->setBlockStateId($x & 0xf, $y, $z & 0xf, $block); }
    public function isInWorld(int $x, int $y, int $z) : bool { return $y >= 0 && $y < 256; }
    public function getMinY() : int { return 0; }
    public function getMaxY() : int { return 256; }
    public function getBlockAt(int $x, int $y, int $z) : Block { return VanillaBlocks::AIR(); }
    public function setBlockAt(int $x, int $y, int $z, Block $block) : void {}
}

$chunk = new Chunk([], false);
$gen = new OpalGenerator(12345, "");
$mgr = new DummyManager($chunk);
$gen->generateChunk($mgr, 16, 16);
$gen->populateChunk($mgr, 16, 16);

echo "Inspection de la colonne X=0, Z=0 du chunk (16, 16) [Monde: 256, 256]:\n";
$converter = TypeConverter::getInstance();
$blockTranslator = $converter->getBlockTranslator();
$dictionary = $blockTranslator->getBlockStateDictionary();

$unknownCount = 0;
for ($y = 0; $y <= 120; $y++) {
    $stateId = $chunk->getBlockStateId(0, $y, 0);
    if ($stateId !== 0) {
        $netId = $blockTranslator->internalIdToNetworkId($stateId);
        $data = $dictionary->generateDataFromStateId($netId);
        $name = $data !== null ? $data->getName() : "INCONNU";
        if ($data === null) {
            $unknownCount++;
            echo "ERREUR INCONNU: Y=$y : StateId=$stateId, NetId=$netId\n";
        }
    }
}

if ($unknownCount === 0) {
    echo "TOUS LES BLOCS DE LA COLONNE SONT VALIDES DANS LA TABLE DU SERVEUR !\n";
}

// Vérifions TOUS les blocs du chunk entier
$chunkUnknown = 0;
for ($cx = 0; $cx < 16; $cx++) {
    for ($cz = 0; $cz < 16; $cz++) {
        for ($cy = 0; $cy < 256; $cy++) {
            $sId = $chunk->getBlockStateId($cx, $cy, $cz);
            if ($sId !== 0) {
                $nId = $blockTranslator->internalIdToNetworkId($sId);
                if ($dictionary->generateDataFromStateId($nId) === null) {
                    $chunkUnknown++;
                }
            }
        }
    }
}
echo "Blocs inconnus dans tout le chunk: $chunkUnknown\n";
