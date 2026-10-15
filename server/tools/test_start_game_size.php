<?php
require 'pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\LevelSettings;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use pocketmine\network\mcpe\protocol\types\NetworkPermissions;
use pocketmine\network\mcpe\protocol\types\ServerTelemetryData;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\math\Vector3;
use Ramsey\Uuid\Uuid;
use pmmp\encoding\ByteBufferWriter;

$tc = TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

$entries = [];
foreach ($states as $state) {
    $stateData = $state->generateStateData();
    $statesTag = CompoundTag::create();
    foreach ($stateData->getStates() as $k => $v) {
        $statesTag->setTag($k, $v);
    }
    $entries[] = new BlockPaletteEntry($stateData->getName(), new CacheableNbt($statesTag));
}

$levelSettings = new LevelSettings();
$levelSettings->seed = -1;
$levelSettings->spawnSettings = new \pocketmine\network\mcpe\protocol\types\SpawnSettings(0, "", 0);
$levelSettings->worldGamemode = 0;
$levelSettings->difficulty = 1;
$levelSettings->spawnPosition = new \pocketmine\network\mcpe\protocol\types\BlockPosition(256, 86, 256);
$levelSettings->hasAchievementsDisabled = true;
$levelSettings->time = 1000;
$levelSettings->eduEditionOffer = 0;
$levelSettings->rainLevel = 0.0;
$levelSettings->lightningLevel = 0.0;
$levelSettings->commandsEnabled = true;
$levelSettings->gameRules = [];
$levelSettings->experiments = new \pocketmine\network\mcpe\protocol\types\Experiments([], false);

$pk = StartGamePacket::create(
    1, 1, 0, new Vector3(256, 73, 256), 0, 0,
    new CacheableNbt(CompoundTag::create()),
    $levelSettings, "", "MOTD", "", false,
    new PlayerMovementSettings(0, true), 0, 0, "", true, "v1.26.52",
    Uuid::fromString(Uuid::NIL), false, false,
    new NetworkPermissions(disableClientSounds: true),
    true, null, new ServerTelemetryData("", "", "", ""),
    $entries, 0
);

$writer = new ByteBufferWriter();
$start = microtime(true);
$pk->encode($writer);
$raw = $writer->getData();
$rawSize = strlen($raw);
$compressed = function_exists('snappy_compress') ? snappy_compress($raw) : "";
$compressedSize = strlen($compressed);
$zlibCompressed = zlib_encode($raw, ZLIB_ENCODING_DEFLATE, 6);
$zlibSize = strlen($zlibCompressed);

echo sprintf("StartGamePacket avec %d entrées:\n", count($entries));
echo sprintf("  Taille brute: %d octets (%.2f Mo)\n", $rawSize, $rawSize / 1024 / 1024);
echo sprintf("  Taille Snappy: %d octets (%.2f Ko)\n", $compressedSize, $compressedSize / 1024);
echo sprintf("  Taille Deflate: %d octets (%.2f Ko)\n", $zlibSize, $zlibSize / 1024);
echo sprintf("  Encodage pris: %.2f ms\n", (microtime(true) - $start) * 1000);
