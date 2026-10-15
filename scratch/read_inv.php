<?php
require 'server/pmmp_src/vendor/autoload.php';
use pocketmine\nbt\BigEndianNbtSerializer;

$data = @file_get_contents('server/players/katsun1246.dat');
if(!$data) exit("no dat\n");
$decompressed = @gzdecode($data);
if ($decompressed !== false) $data = $decompressed;
$nbt = (new BigEndianNbtSerializer())->read($data)->mustGetCompoundTag();
if($inv = $nbt->getListTag('Inventory')){
    echo "Inventory items count: " . count($inv) . "\n";
    foreach($inv as $item){
        echo " - " . $item->getString('Name', '') . " x " . $item->getByte('Count', 0) . " Slot: " . $item->getByte('Slot', 0) . "\n";
    }
}else{
    echo "No inventory tag\n";
}
