<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\network\mcpe\convert\TypeConverter;

$worldPath = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/worlds/world';
$logger = new class extends \SimpleLogger { public function log($l, $m) : void{} };
$db = new LevelDB($worldPath, $logger);
$typeConverter = TypeConverter::getInstance();
$blockTranslator = $typeConverter->getBlockTranslator();
$dict = $blockTranslator->getBlockStateDictionary();

$loaded = $db->loadChunk(16, 16);
$cd = $loaded->getData();
$subs = $cd->getSubChunks();

echo "Blocks at X=256, Z=256 (rel: x=0, z=0 in chunk 16,16):\n";
for ($y = 0; $y <= 120; $y++) {
    $sy = $y >> 4;
    $sub = $subs[$sy] ?? null;
    if ($sub !== null) {
        $stateId = $sub->getBlockStateId(0, $y & 0x0f, 0);
        if ($stateId !== 0) {
            $netId = $blockTranslator->internalIdToNetworkId($stateId);
            $sd = $dict->generateDataFromStateId($netId);
            echo sprintf("  Y=%3d : StateId=%d, NetId=%d (%s)\n", $y, $stateId, $netId, $sd?->getName());
        }
    }
}
