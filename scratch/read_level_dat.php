<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

$raw = file_get_contents(__DIR__ . '/../server/worlds/world/level.dat');
$rawNbt = substr($raw, 8); // Bedrock header is 8 bytes
$ser = new LittleEndianNbtSerializer();
$data = $ser->read($rawNbt)->mustGetCompoundTag();

echo "Spawn: X=" . $data->getInt("SpawnX", -999) . " Y=" . $data->getInt("SpawnY", -999) . " Z=" . $data->getInt("SpawnZ", -999) . "\n";
echo "LevelName: " . $data->getString("LevelName", "N/A") . "\n";
echo "Generator: " . $data->getInt("Generator", -1) . "\n";
echo "StorageVersion: " . $data->getInt("StorageVersion", -1) . "\n";
echo "NetworkVersion: " . $data->getInt("NetworkVersion", -1) . "\n";
echo "GameType: " . $data->getInt("GameType", -1) . "\n";
