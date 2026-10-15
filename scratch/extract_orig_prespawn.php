<?php
$phar = new Phar("server/PocketMine-MP.phar.bak");
foreach (new RecursiveIteratorIterator($phar) as $f) {
    if (strpos($f->getPathname(), "PreSpawnPacketHandler") !== false) {
        echo $f->getPathname() . "\n";
        echo file_get_contents($f->getPathname());
    }
}
