<?php
require 'pmmp_src/vendor/autoload.php';

$tc = \pocketmine\network\mcpe\convert\TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

echo "--- 30 PREMIERS BLOCS ---\n";
for ($i = 0; $i < 30; $i++) {
    echo sprintf("  #%5d : %s\n", $i, $states[$i]->getStateName());
}

echo "\n--- 30 DERNIERS BLOCS ---\n";
$total = count($states);
for ($i = $total - 30; $i < $total; $i++) {
    echo sprintf("  #%5d : %s\n", $i, $states[$i]->getStateName());
}
