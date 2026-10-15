<?php

$dir = 'pmmp_src/vendor/pocketmine/bedrock-data';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir()) continue;
    $ext = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
    if (!in_array($ext, ['json', 'bin', 'nbt'])) continue;
    if (str_contains($file->getPathname(), '.bak')) continue;

    $content = file_get_contents($file->getPathname());
    $count = substr_count($content, 'sulfur') + substr_count($content, 'cinnabar');
    if ($count > 0) {
        echo $file->getPathname() . " -> contains sulfur/cinnabar ($count occurrences)\n";
    }
}
