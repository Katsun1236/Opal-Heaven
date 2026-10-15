<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

$raw = file_get_contents('server/pmmp_src/vendor/pocketmine/bedrock-data/canonical_block_states.nbt');
echo "canonical_block_states.nbt size: " . strlen($raw) . " bytes\n";

$serializer = new \pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer();
// Count entries
$count = 0;
$offset = 0;
$first = null;
$last = null;
while ($offset < strlen($raw)) {
    try {
        $tree = $serializer->read($raw, $offset, 512);
        $tag = $tree->getTag();
        if ($first === null) $first = $tag->getString("name");
        $last = $tag->getString("name");
        $count++;
    } catch (\Throwable $e) {
        break;
    }
}
echo "Total canonical block states: $count\n";
echo "First: $first | Last: $last\n";
