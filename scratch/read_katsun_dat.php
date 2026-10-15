<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;

$data = file_get_contents('server/players/katsun1246.dat');
$decompressed = @gzdecode($data);
if ($decompressed !== false) $data = $decompressed;

$nbt = (new BigEndianNbtSerializer())->read($data)->mustGetCompoundTag();
echo "Player pos:\n";
foreach ($nbt->getListTag("Pos") as $val) {
    echo "  " . $val->getValue() . "\n";
}
echo "Level: " . $nbt->getString("Level", "none") . "\n";
