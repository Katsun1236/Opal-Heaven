<?php

require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\nbt\tag\ListTag;

$raw = file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/entity_identifiers.nbt');
$serializer = new NetworkNbtSerializer();
$tag = $serializer->read($raw)->mustGetCompoundTag();

$idList = $tag->getListTag("idlist");
echo "Total entities in idlist: " . $idList->count() . "\n";

$removed = 0;
foreach ($idList as $k => $compound) {
    $id = $compound->getString("id");
    if (str_contains($id, "sulfur") || str_contains($id, "cinnabar")) {
        echo "Found in entity_identifiers: index $k, id=$id\n";
    }
}
