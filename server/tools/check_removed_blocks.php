<?php
require 'pmmp_src/vendor/autoload.php';

$tc = \pocketmine\network\mcpe\convert\TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();
$sulfurCount = 0;
$cinnabarCount = 0;
$matchingBlocks = [];
foreach ($states as $i => $s) {
    $name = $s->getStateName();
    if (str_contains($name, 'sulfur') || str_contains($name, 'cinnabar')) {
        $matchingBlocks[$name] = ($matchingBlocks[$name] ?? 0) + 1;
        if (str_contains($name, 'sulfur')) $sulfurCount++;
        if (str_contains($name, 'cinnabar')) $cinnabarCount++;
    }
}
echo "Sulfur states count: $sulfurCount\n";
echo "Cinnabar states count: $cinnabarCount\n";
echo "Matching block names:\n";
foreach ($matchingBlocks as $name => $count) {
    echo "  $name: $count states\n";
}
