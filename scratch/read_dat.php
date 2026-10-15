<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;

$raw = file_get_contents('server/players/katsun1246.dat');
$decomp = @gzdecode($raw);
$data = ($decomp !== false) ? $decomp : $raw;
$tag = (new BigEndianNbtSerializer())->read($data)->mustGetCompoundTag();

foreach (["Pos", "Motion", "Rotation", "SpawnX", "SpawnY", "SpawnZ", "Level"] as $k) {
    if ($tag->getTag($k) !== null) {
        echo "$k: " . $tag->getTag($k)->toString() . PHP_EOL;
    }
}
