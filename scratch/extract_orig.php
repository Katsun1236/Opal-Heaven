<?php
$phar = new Phar("server/PocketMine-MP.phar");
foreach (new RecursiveIteratorIterator($phar) as $file) {
    if (strpos($file->getPathname(), "PreSpawn") !== false) {
        echo $file->getPathname() . "\n";
        file_put_contents("scratch/orig_prespawn.php", file_get_contents($file->getPathname()));
    }
}
