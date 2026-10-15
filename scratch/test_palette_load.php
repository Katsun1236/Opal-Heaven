<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\utils\Filesystem;
use pocketmine\data\bedrock\BedrockDataFiles;

$raw = Filesystem::fileGetContents(BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
$serializer = new NetworkNbtSerializer();

$palette = [];
$offset = 0;
$len = strlen($raw);
while ($offset < $len) {
    try {
        $tree = $serializer->read($raw, $offset, 512);
        $tag = $tree->getTag();
        $name = $tag->getString("name");
        $states = $tag->getCompoundTag("states") ?? new \pocketmine\nbt\tag\CompoundTag();
        $palette[] = new BlockPaletteEntry($name, new CacheableNbt($states));
    } catch (\Throwable $e) {
        break;
    }
}

echo "Loaded " . count($palette) . " block palette entries.\n";
