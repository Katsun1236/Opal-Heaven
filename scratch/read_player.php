<?php
require 'server/pmmp_src/vendor/autoload.php';
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\utils\Filesystem;

$raw = Filesystem::fileGetContents('server/players/katsun1246.dat');
$decompressed = @zlib_decode($raw);
if($decompressed === false) $decompressed = $raw;
$serializer = new BigEndianNbtSerializer();
$nbt = $serializer->read($decompressed)->mustGetCompoundTag();
echo $nbt->toString() . "\n";
