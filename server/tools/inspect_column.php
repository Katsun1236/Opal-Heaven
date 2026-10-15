<?php
require 'pmmp_src/vendor/autoload.php';

$logger = new class extends \SimpleLogger { public function log($l, $m) : void {} };
$db = new \pocketmine\world\format\io\leveldb\LevelDB('worlds/world', $logger);
$loaded = $db->loadChunk(16, 16);
if ($loaded === null) {
    echo "Chunk (16, 16) is null!\n";
    exit(1);
}
$chunk = $loaded->getData();
$tc = \pocketmine\network\mcpe\convert\TypeConverter::getInstance();
$bt = $tc->getBlockTranslator();
$dict = $bt->getBlockStateDictionary();

echo "Inspect column at X=256, Z=256 (local 0, Y, 0):\n";
for ($y = 50; $y <= 95; $y++) {
    $subChunk = $chunk->getSubChunks()[$y >> 4] ?? null;
    $sid = $subChunk !== null ? $subChunk->getBlockStateId(0, $y & 0x0f, 0) : 0;
    $netId = $bt->internalIdToNetworkId($sid);
    $data = $dict->generateDataFromStateId($netId);
    $name = $data?->getName() ?? "unknown";
    if ($sid !== 0 || ($y >= 70 && $y <= 88)) {
        echo sprintf("  Y=%2d : StateId=%8d | NetId=%5d | %s\n", $y, $sid, $netId, $name);
    }
}
