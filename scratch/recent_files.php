<?php
$dir = 'server/pmmp_src';
$cutoff = strtotime('2026-10-14 12:00:00');

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile()) {
        if ($file->getMTime() > $cutoff) {
            echo date('Y-m-d H:i:s', $file->getMTime()) . " - " . $file->getPathname() . "\n";
        }
    }
}
