<?php
require 'pmmp_src/vendor/autoload.php';

$tc = \pocketmine\network\mcpe\convert\TypeConverter::getInstance();
$states = $tc->getBlockTranslator()->getBlockStateDictionary()->getStates();

$firstSulfur = null;
$lastSulfur = null;
$firstCinnabar = null;
$lastCinnabar = null;

foreach ($states as $i => $s) {
    $name = $s->getStateName();
    if (str_contains($name, 'sulfur')) {
        if ($firstSulfur === null) $firstSulfur = $i;
        $lastSulfur = $i;
    }
    if (str_contains($name, 'cinnabar')) {
        if ($firstCinnabar === null) $firstCinnabar = $i;
        $lastCinnabar = $i;
    }
}

echo "Sulfur index range: $firstSulfur to $lastSulfur\n";
echo "Cinnabar index range: $firstCinnabar to $lastCinnabar\n";

// Regardons les blocs de notre spawn :
$spawnBlocks = [
    "air" => 13094,
    "bedrock" => 13805,
    "deepslate" => 1311,
    "stone" => 2706,
    "podzol" => 7825,
    "warped_nylium" => 12734,
];

foreach ($spawnBlocks as $bname => $idx) {
    echo sprintf("  Bloc %-15s : Index %5d (est après sulfur? %s)\n", $bname, $idx, $idx > $firstSulfur ? "OUI !" : "non");
}
