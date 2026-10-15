<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\network\mcpe\convert\TypeConverter;

$worldPath = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/worlds/world';
echo "Inspect spawn area blocks around 256, 73, 256 in $worldPath...\n";

$logger = new class extends \SimpleLogger { public function log($l, $m) : void{ echo "[$l] $m\n"; } };
try {
    $db = new LevelDB($worldPath, $logger);
    $typeConverter = TypeConverter::getInstance();
    $blockTranslator = $typeConverter->getBlockTranslator();
    $dict = $blockTranslator->getBlockStateDictionary();

    $uniqueBlocks = [];
    $totalBlocks = 0;
    // Spawn is at (256, 256) which is Chunk X=16, Z=16
    for ($cx = 15; $cx <= 17; $cx++) {
        for ($cz = 15; $cz <= 17; $cz++) {
            $loadedData = $db->loadChunk($cx, $cz);
            if ($loadedData === null) {
                echo "Chunk ($cx, $cz) is NULL!\n";
                continue;
            }
            $chunkData = $loadedData->getData();
            foreach ($chunkData->getSubChunks() as $sy => $subChunk) {
                for ($x = 0; $x < 16; $x++) {
                    for ($z = 0; $z < 16; $z++) {
                        for ($y = 0; $y < 16; $y++) {
                            $stateId = $subChunk->getBlockStateId($x, $y, $z);
                            if ($stateId !== 0) { // Pas air
                                $totalBlocks++;
                                if (!isset($uniqueBlocks[$stateId])) {
                                    $netId = $blockTranslator->internalIdToNetworkId($stateId);
                                    $stateData = $dict->generateDataFromStateId($netId);
                                    $name = $stateData?->getName() ?? "UNKNOWN";
                                    $uniqueBlocks[$stateId] = [
                                        "name" => $name,
                                        "netId" => $netId,
                                        "count" => 1
                                    ];
                                } else {
                                    $uniqueBlocks[$stateId]["count"]++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    echo "Total non-air blocks inspected: $totalBlocks\n";
    echo "Found " . count($uniqueBlocks) . " unique non-air block states around spawn (3x3 chunks):\n";
    foreach ($uniqueBlocks as $sid => $info) {
        echo sprintf("  StateId %d -> NetId %d : %s (count: %d)\n", $sid, $info['netId'], $info['name'], $info['count']);
    }

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
