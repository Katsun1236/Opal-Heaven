<?php
$names = [
    "minecraft:cyan_terracotta",
    "minecraft:hard_pink_stained_glass",
    "minecraft:blue_candle",
    "minecraft:dark_oak_wood",
    "minecraft:birch_standing_sign",
    "minecraft:polished_basalt",
    "minecraft:nether_gold_ore",
    "minecraft:zombie_head",
    "minecraft:waxed_weathered_copper_chain",
    "minecraft:waxed_weathered_copper_chest"
];

function fnv1a_32(string $str) : int {
    $h = 0x811c9dc5;
    for ($i = 0; $i < strlen($str); $i++) {
        $h ^= ord($str[$i]);
        $h = ($h * 0x01000193) & 0xffffffff;
    }
    return $h;
}

function fnv1_32(string $str) : int {
    $h = 0x811c9dc5;
    for ($i = 0; $i < strlen($str); $i++) {
        $h = ($h * 0x01000193) & 0xffffffff;
        $h ^= ord($str[$i]);
    }
    return $h;
}

echo "Testing hashes on first names:\n";
foreach ($names as $name) {
    echo sprintf("%-40s | crc32: %08x | fnv1a: %08x | fnv1: %08x\n", $name, crc32($name), fnv1a_32($name), fnv1_32($name));
}
