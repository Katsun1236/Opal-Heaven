<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\LittleEndianNbtSerializer;

$files = ['server/worlds/world/level.dat', 'server/worlds/world_backup/level.dat'];
foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "$file does not exist\n";
        continue;
    }
    echo "=== $file ===\n";
    $content = file_get_contents($file);
    // Usually Bedrock level.dat has 8 bytes header (version + length), then LE NBT
    $headerVer = unpack('V', substr($content, 0, 4))[1] ?? 0;
    $headerLen = unpack('V', substr($content, 4, 4))[1] ?? 0;
    echo "Header: ver=$headerVer, len=$headerLen, total=" . strlen($content) . "\n";
    $nbtData = substr($content, 8);
    try {
        $serializer = new LittleEndianNbtSerializer();
        $tag = $serializer->read($nbtData)->mustGetCompoundTag();
        echo "generatorName: " . ($tag->getTag('generatorName')?->getValue() ?? 'N/A') . "\n";
        echo "generator: " . ($tag->getTag('generator')?->getValue() ?? 'N/A') . "\n";
        echo "LevelName: " . ($tag->getTag('LevelName')?->getValue() ?? 'N/A') . "\n";
        echo "Spawn: " . ($tag->getTag('SpawnX')?->getValue() ?? '?') . ", " . ($tag->getTag('SpawnY')?->getValue() ?? '?') . ", " . ($tag->getTag('SpawnZ')?->getValue() ?? '?') . "\n";
    } catch (\Throwable $e) {
        echo "Failed to decode with LE serializer: " . $e->getMessage() . "\n";
    }
}
