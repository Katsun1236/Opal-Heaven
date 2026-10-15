<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';
\pocketmine\Bootstrap::init();

$tests = ['amethyst_block', 'calcite', 'smooth_basalt', 'moss_block', 'prismarine', 'obsidian', 'packed_ice', 'purpur_block', 'crying_obsidian'];
foreach ($tests as $t) {
    $item = \pocketmine\item\StringToItemParser::getInstance()->parse($t);
    if ($item !== null) {
        $block = $item->getBlock();
        echo "Parsed '$t' -> " . $block->getName() . " (State ID: " . $block->getStateId() . ")\n";
    } else {
        echo "FAILED to parse '$t'\n";
    }
}
