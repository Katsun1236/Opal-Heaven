<?php
require 'server/pmmp_src/vendor/autoload.php';

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\JigsawStructureDataPacket;
use pocketmine\network\mcpe\protocol\VoxelShapesPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pmmp\encoding\ByteBufferWriter;

$emptyJigsawNbt = CompoundTag::create()
    ->setTag("processors", new ListTag([], NBT::TAG_Compound))
    ->setTag("template_pools", new ListTag([], NBT::TAG_Compound))
    ->setTag("jigsaws", new ListTag([], NBT::TAG_Compound))
    ->setTag("structure_sets", new ListTag([], NBT::TAG_Compound));

$jigsawPacket = JigsawStructureDataPacket::create(new CacheableNbt($emptyJigsawNbt));
$w1 = new ByteBufferWriter();
$jigsawPacket->encode($w1);
echo "Jigsaw packet encoded size: " . strlen($w1->getData()) . " bytes\n";
echo "Jigsaw hex: " . bin2hex($w1->getData()) . "\n";

$voxelPacket = VoxelShapesPacket::create([], [], 0);
$w2 = new ByteBufferWriter();
$voxelPacket->encode($w2);
echo "VoxelShapes packet encoded size: " . strlen($w2->getData()) . " bytes\n";
echo "VoxelShapes hex: " . bin2hex($w2->getData()) . "\n";
echo "ALL OK!\n";

