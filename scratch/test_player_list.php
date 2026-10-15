<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\types\skin\SkinImage;
use Ramsey\Uuid\Uuid;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\ByteBufferReader;

$skinData = new SkinData(
    "Standard_Custom",
    "",
    '{"geometry":{"default":"geometry.humanoid.custom"}}',
    new SkinImage(64, 64, str_repeat("\x00", 64 * 64 * 4)),
    [],
    new SkinImage(0, 0, ""),
    ""
);

$entry = PlayerListEntry::createAdditionEntry(
    Uuid::uuid4(),
    1,
    "Katsun1246",
    $skinData,
    "123456789"
);

$pk = PlayerListPacket::add([$entry]);
$w = new ByteBufferWriter();
$pk->encode($w);

$data = $w->getData();
echo "PlayerListPacket encoded size: " . strlen($data) . " bytes\n";

$r = new ByteBufferReader($data);
$pkDecoded = new PlayerListPacket();
$pkDecoded->decode($r);
echo "Decoded count: " . count($pkDecoded->entries) . "\n";
echo "Username: " . $pkDecoded->entries[0]->username . "\n";
echo "SUCCESS!\n";
