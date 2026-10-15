<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';
$data = file_get_contents('c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/worlds/world/level.dat');
$nbt = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read(substr($data, 8))->mustGetCompoundTag();
foreach ($nbt as $k => $v) {
    echo $k . ": " . (string)$v->getValue() . "\n";
}
