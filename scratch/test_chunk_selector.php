<?php
require 'server/pmmp_src/vendor/autoload.php';

$cs = new \pocketmine\player\ChunkSelector();
$i = 0;
foreach ($cs->selectChunks(4, 16, 16) as $r => $h) {
    \pocketmine\world\World::getXZ($h, $x, $z);
    echo "#$i (r=$r): ($x, $z)\n";
    $i++;
    if ($i >= 20) break;
}
