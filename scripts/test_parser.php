<?php
require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

$blockNames = [
    'moss', 'magma', 'moss_block', 'magma_block', 'sculk', 'sculk_catalyst', 'mud', 'packed_mud', 'mud_bricks'
];

$parser = \pocketmine\item\StringToItemParser::getInstance();

foreach ($blockNames as $name) {
    $item = $parser->parse($name);
    if ($item !== null) {
        $block = $item->getBlock();
        echo "[OK] '$name' -> " . $block->getName() . "\n";
    } else {
        echo "[FAIL] '$name'\n";
    }
}
