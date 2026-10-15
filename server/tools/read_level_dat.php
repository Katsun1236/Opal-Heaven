<?php
require 'pmmp_src/vendor/autoload.php';

$content = file_get_contents('worlds/world/level.dat');
$nbt = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read(substr($content, 8))->mustGetCompoundTag();
foreach ($nbt as $k => $v) {
    if ($v instanceof \pocketmine\nbt\tag\CompoundTag || $v instanceof \pocketmine\nbt\tag\ListTag) {
        echo "$k => " . get_class($v) . " (count " . $v->count() . ")\n";
    } else {
        echo "$k => " . $v->getValue() . "\n";
    }
}
