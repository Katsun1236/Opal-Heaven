<?php
require 'pmmp_src/vendor/autoload.php';

$content = file_get_contents('worlds/world/level.dat');
$nbt = (new \pocketmine\nbt\LittleEndianNbtSerializer())->read(substr($content, 8))->mustGetCompoundTag();
echo "NetworkVersion: " . ($nbt->getTag('NetworkVersion')?->getValue() ?? "none") . "\n";
$ver = $nbt->getListTag('lastOpenedWithVersion');
if ($ver !== null) {
    echo "lastOpenedWithVersion: " . implode('.', array_map(fn($t) => $t->getValue(), $ver->getValue())) . "\n";
}
