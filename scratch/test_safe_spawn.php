<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\World;

// Let's inspect World::getSafeSpawn implementation:
echo "Inspecting World getSafeSpawn...\n";
